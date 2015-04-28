<?php

namespace PG\PartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

use NW\UserBundle\Form\Type\RegistroPartyType;

class UsersController extends Controller
{
    public function registroAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();

    	// Formulario de registro
        $formRegistroPary = $this->createForm(new RegistroPartyType);

        // Recuperando formulario de registro
        $formRegistroPary->handleRequest($request);

        if('POST' === $request->getMethod())
        {
            // Registrar usuario
            $userManager = $this->get('fos_user.user_manager');

            // Checando si ya existe el usuario o el correo
            $usuarioExistente = $userManager->findUserBy(array('username' => $formRegistroPary["email"]->getData()));

            if($usuarioExistente)
            {
                $this->get('session')->getFlashBag()->add('notice', 'Este correo ya está en uso');
                return $this->render('PGPartyBundle:Users:registro.html.twig', array(
                    'formRegistroPary' => $formRegistroPary->createView(),
                    'emailpasado' => $request->query->get('email'),
                ));
            }

            $user = $userManager->createUser(); 

            $password = $formRegistroPary["userPass"]->getData();

            $user->setUsername($formRegistroPary["email"]->getData());
            $user->setEmail($formRegistroPary["email"]->getData());
            $user->setPlainPassword($password);
            $user->setMoneda($formRegistroPary["moneda"]->getData());
            $user->setEnabled(true);
            $user->setSaldo(0);
            $user->setNombre($formRegistroPary["nombre"]->getData());
            $user->setApellidos($formRegistroPary["apellidos"]->getData());

            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'secure_area', array('ROLE_USER'));
            $this->get('security.context')->setToken($token);
            $this->get( 'event_dispatcher' )->dispatch(AuthenticationEvents::AUTHENTICATION_SUCCESS, new AuthenticationEvent( $token ) );

            // Envio de correo de registro exitoso
            $message = \Swift_Message::newInstance()
            ->setSubject("Te registraste con éxito en PartyGift")
            ->setFrom("info@newlywishes.com")
            ->setTo($formRegistroPary["email"]->getData())
            ->setContentType("text/html")
            ->setBody(
                $this->renderView(
                    'PGPartyBundle:Users:correoRegistroExitoso.html.twig', array(
                        'password' => $password,
                        'email' => $formRegistroPary["email"]->getData(),
                    )
                )
            );
            $this->get('mailer')->send($message);

            // Operaciones de registro exitoso
            return $this->render('PGPartyBundle:Users:registroExitoso.html.twig');

	   }

        return $this->render('PGPartyBundle:Users:registro.html.twig', array(
        	'formRegistroPary' => $formRegistroPary->createView(),
            'emailpasado' => $request->query->get('email'),
        ));
    }

    // Pagina que checa si el usuario ya está registrado y responde con JSON
    public function checarmicuentaAction()
    {
        // Container de seguridad (para revisar si hay logueo)
        $securityContext = $this->container->get('security.context');

        // Si el usuario está logueado
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) // true es que sí está logueado
        {
            // La petición funciona
            $return["responseCode"] = 200;
            $responseCode = 200;
            $return = json_encode($return);
        }
        else{
            // La petición no funciona
            $return["responseCode"] = 500;
            $responseCode = 500;
            $return = json_encode($return);
        }

        // Regresa el resultado en JSON
        return new Response($return, $responseCode, array('Content-Type'=>'application/json'));
    }
}