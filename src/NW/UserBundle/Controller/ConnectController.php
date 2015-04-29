<?php
namespace NW\UserBundle\Controller;

use HWI\Bundle\OAuthBundle\Controller\ConnectController as BaseConnectController;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class ConnectController extends BaseConnectController
{
    /**
     * Shows a registration form if there is no user logged in and connecting
     * is enabled.
     *
     * @param Request $request A request.
     * @param string  $key     Key used for retrieving the right information for the registration form.
     *
     * @return Response
     *
     * @throws NotFoundHttpException if `connect` functionality was not enabled
     * @throws AccessDeniedException if any user is authenticated
     * @throws \Exception
     */
    public function registrationAction(Request $request, $key)
    {
        $connect = $this->container->getParameter('hwi_oauth.connect');
        if (!$connect) {
            throw new NotFoundHttpException();
        }

        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if ($hasUser) {
            throw new AccessDeniedException('Cannot connect already registered account.');
        }

        $session = $request->getSession();
        $error = $session->get('_hwi_oauth.registration_error.'.$key);
        $session->remove('_hwi_oauth.registration_error.'.$key);

        if (!($error instanceof AccountNotLinkedException) || (time() - $key > 300)) {
            // todo: fix this
            throw new \Exception('Cannot register an account.');
        }

        $userInformation = $this
            ->getResourceOwnerByName($error->getResourceOwnerName())
            ->getUserInformation($error->getRawToken())
        ;

        // enable compatibility with FOSUserBundle 1.3.x and 2.x
        if (interface_exists('FOS\UserBundle\Form\Factory\FactoryInterface')) {
            $form = $this->container->get('hwi_oauth.registration.form.factory')->createForm();
        } else {
            $form = $this->container->get('hwi_oauth.registration.form');
        }

        $formHandler = $this->container->get('hwi_oauth.registration.form.handler');
        if ($formHandler->process($request, $form, $userInformation)) {
            $this->container->get('hwi_oauth.account.connector')->connect($form->getData(), $userInformation);

            // Authenticate the user
            $this->authenticateUser($request, $form->getData(), $error->getResourceOwnerName(), $error->getRawToken());

            $quedatos = $form->getData();

            // Setear saldo en el usuario
            $em = $this->container->get('doctrine.orm.entity_manager');
            $usersRepository = $em->getRepository('NWUserBundle:User');
            $miUsuario = $usersRepository->find($quedatos->getId());

            $miUsuario->setSaldo(0);
            $em->persist($miUsuario);
            $em->flush();

            // Respuesta HTML
            /*$respuestaHTML = "<script language=\"javascript\">
                                window.onunload = refreshParent;
                                function refreshParent() {
                                    window.opener.location.href = \"".$this->container->get('router')->generate('pg_party_miCuenta')."\";
                                }
                                self.close();
                            </script>";

            return new Response($respuestaHTML);*/

            return new RedirectResponse($this->container->get('router')->generate('pg_party_miCuenta'));
        }

        // reset the error in the session
        $key = time();
        $session->set('_hwi_oauth.registration_error.'.$key, $error);

        // Crear un nombre de usuario con base en el nombre de usuario de facebook
        $nickname = $this->quitarAcentos($userInformation->getNickname());

        // Manejador de usuarios de fosuserbundle
        $userManager = $this->container->get('fos_user.user_manager'); 

        // Checando si ya existe el usuario o el correo
        $usuarioPorUsername = $userManager->findUserBy(array('username' => $nickname));
        
        // Si ya existe el usuario se manda mensaje de que ya existe el usuario
        if($usuarioPorUsername)
        {
            $this->get('session')->getFlashBag()->add('notice', 'Este correo ya está en uso');
        }
        else
        {
            // Generar contraseña aleatoria
            $password = $this->randomPassword();

            // Envio de correo de registro exitoso
            $message = \Swift_Message::newInstance()
            ->setSubject("Te registraste con éxito en PartyGift")
            ->setFrom("info@newlywishes.com")
            ->setTo($userInformation->getEmail())
            ->setContentType("text/html")
            ->setBody(
                $this->container->get('templating')->render(
                    'PGPartyBundle:Users:correoRegistroExitosoFacebook.html.twig', array(
                        'password' => $password,
                        'email' => $userInformation->getEmail(),
                    )
                )
            );
            $this->container->get('swiftmailer.mailer')->send($message); 
        }

        return $this->container->get('templating')->renderResponse('HWIOAuthBundle:Connect:registration.html.' . $this->getTemplatingEngine(), array(
            'key' => $key,
            'form' => $form->createView(),
            'userInformation' => $userInformation,
            'nickname' => $nickname,
            'password' => $password,
        ));
    }

    private function quitarAcentos($palabra)
    {
        $no_permitidas = array ('á','é','í','ó','ú','Á','É','Í','Ó','Ú','ñ','Ñ',' ');
        $si_permitidas = array ('a','e','i','o','u','A','E','I','O','U','n','N', '');

        $palabra = str_replace($no_permitidas, $si_permitidas, $palabra);

        return $palabra;
    }

    private function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}