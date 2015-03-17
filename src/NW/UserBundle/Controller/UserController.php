<?php

namespace NW\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use NW\UserBundle\Entity\usuario;
use NW\UserBundle\Entity\Novias;
use NW\UserBundle\Entity\Novios;
use NW\UserBundle\Entity\registroproveedores;
use NW\UserBundle\Entity\Reportero;
use NW\PrincipalBundle\Entity\Bodas;

use NW\UserBundle\Form\Type\PostRegistroType;
use NW\UserBundle\Form\Type\RegistroType;
use NW\UserBundle\Form\Type\ReporteroType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserController extends Controller
{

    public function pruebasAction()
    {
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
        return new Response('Hola mundo');
    }

    public function loginnoviosAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
 
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        }
        else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'NWUserBundle:User:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }

    public function loginproveedoresAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
 
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        }
        else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'NWUserBundle:User:loginProveedores.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }

    public function changePassAction($id)
    {
        $request = $this->getRequest();

        // Se quiere cambiar la contraseña
        if($id)
        {
            // Buscar el usuario
            $userEntity = $this->getDoctrine()->getManager()->getRepository('NWUserBundle:User');
            $user = $userEntity->find($id);
            $username = $user->getUsername();

            // Inicio de Sesión
            $session = $request->getSession();
     
            // get the login error if there is one
            if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
                $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
                );
            }
            else {
                $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
                $session->remove(SecurityContext::AUTHENTICATION_ERROR);
            }

            // Formulario de cambio de contraseña
            $formChangePass = $this->createFormBuilder()
                ->add('newPass', 'password')
                ->add('Cambiar', 'submit')
                ->getForm();

            $exitoActualizacion = false;

            // Obtener formuario de cambio de contraseña
            $formChangePass->handleRequest($request);
            if($formChangePass->isValid())
            {
                // Cambiar contraseña del usuario
                $user->setPlainPassword($formChangePass["newPass"]->getData());
                $this->get('fos_user.user_manager')->updateUser($user, false);
                $this->getDoctrine()->getManager()->flush();
                
                // Ya se actualizó la contraseña
                $exitoActualizacion = true;
            }

            return $this->render('NWUserBundle:User:changePass.html.twig', array(
                'exitoActualizacion' => $exitoActualizacion,
                'formChangePass' => $formChangePass->createView(),
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                'username' => $username
            ));

        }

        // Formulario de correo
        $formCorreo = $this->createFormBuilder()
            ->add('email', 'email')
            ->add('Continuar', 'submit')
            ->getForm();

        // Obtener formuario
        $formCorreo->handleRequest($request);
        if($formCorreo->isValid())
        {
            // Buscar el usuario
            $userEntity = $this->getDoctrine()->getManager()->getRepository('NWUserBundle:User');
            $user = $userEntity->findOneBy(array('email' => $formCorreo['email']->getData()));
            $id = $user->getId();

            $redirect = $this->generateUrl('nw_principal_changepass', array('id' => $id));
            return $this->redirect($redirect);
        }

        return $this->render('NWUserBundle:User:cualmail.html.twig', array(
            'formCorreo' => $formCorreo->createView()
        ));
        
    }

    public function postregistronoviosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager(); 

        // Obtener usuario
        $user = $this->getUser();

        // Generando el formulario de registro de los novios y usuario
        $formData['novias'] = new Novias();
        $formData['novios'] = new Novios();
        $form = $this->createForm(new PostRegistroType(), $formData); // Formulario de usuarios mezclado con el de novias y novios

        // Recuperando formularios
        if('POST' === $request->getMethod()) {
            // Formulario de datos de la boda
            if ($request->request->has($form->getName())) {
                // Solicitando datos del formulario para ver si recuperar los datos, mostrar error o mostrar formulario
                $form->handleRequest($request);
                if ($form->isValid()) {
                    // Recuperando datos de los novios
                    $novias = $formData['novias'];
                    $novios = $formData['novios'];

                    // Agregando Estado de la novia
                    $estadoNovia=$this->getDoctrine()->getRepository('NWPrincipalBundle:Estados')->find($form["novias"]["estado"]->getData());
                    $estadoNovia->addNovia($novias);
                    $novias->setEstados($estadoNovia);

                    // Agregando Estado del novio
                    $estadoNovio=$this->getDoctrine()->getRepository('NWPrincipalBundle:Estados')->find($form["novios"]["estado"]->getData());
                    $estadoNovio->addNovio($novios);
                    $novios->setEstados($estadoNovio);

                    // Agregando Usuario a los novios
                    $novias->setUser($user);
                    $novios->setUser($user);

                    // Agregando Novia al Novio
                    $novios->setNovia($novias);

                    // Agregando objeto boda a los novios
                    $boda = new Bodas();
                    $boda->setUser($user);
                    $boda->setCeremonia('');
                    $boda->setCeremoniaDireccion('');
                    $boda->setRecepcion('');
                    $boda->setRecepcionDireccion('');
                    $boda->setFechaBoda(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'));

                    // Setteando rol de novio en el usuario
                    $user->addRole('ROLE_NOVIO');

                    // Persistiendo los datos en la base de datos
                    $em->persist($novias);
                    $em->persist($novios);
                    $em->persist($boda);
                    $em->persist($user);
                    $em->flush();

                    // Enviar correo a los novios por el registro exitoso
                    // Novio
                    $message = \Swift_Message::newInstance()
                    ->setSubject("Te registraste con éxito en NewlyWishes.com")
                    ->setFrom("info@newlywishes.com")
                    ->setTo($novios->getEMail())
                    ->setContentType("text/html")
                    ->setBody(
                        $this->renderView(
                            'NWUserBundle:User:correoRegistroExitosoFacebook.html.twig', array(
                                'user' => $user
                            )
                        )
                    );
                    $this->get('mailer')->send($message);
                    // Novia
                    $message = \Swift_Message::newInstance()
                    ->setSubject("Te registraste con éxito en NewlyWishes.com")
                    ->setFrom("info@newlywishes.com")
                    ->setTo($novias->getEMail())
                    ->setContentType("text/html")
                    ->setBody(
                        $this->renderView(
                            'NWUserBundle:User:correoRegistroExitosoFacebook.html.twig', array(
                                'user' => $user
                            )
                        )
                    );
                    $this->get('mailer')->send($message);                    

                    // Logout del usuario para que entre como cuenta de novio
                    $this->get('security.context')->setToken(null);
                    $this->get('request')->getSession()->invalidate();

                    // El registro del formulario fue exitoso y se muestra mensaje de felicitación
                    return $this->redirect($this->generateUrl('nw_user_registro_exitoso'));
                }
            }
        }

        // Si no se ha ocupado el formulario (o contiene errores) se le muestra al usuario
        return $this->render('NWUserBundle:User:postregistronovios.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function registronoviosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager(); 

        // Generando el formulario de registro de los novios y usuario
        $formData['novias'] = new Novias();
        $formData['novios'] = new Novios();
        $form = $this->createForm(new RegistroType(), $formData); // Formulario de usuarios mezclado con el de novias y novios

        // Recuperando formularios
        if('POST' === $request->getMethod()) {
            // Formulario de datos de la boda
            if ($request->request->has($form->getName())) {
                // Solicitando datos del formulario para ver si recuperar los datos, mostrar error o mostrar formulario
                $form->handleRequest($request);
                if ($form->isValid()) {
                    // Recuperando datos de los novios
                    $novias = $formData['novias'];
                    $novios = $formData['novios'];

                    // Checando si ya existe el usuario o el correo
                    $userManager = $this->get('fos_user.user_manager'); 
                    $usuarioPorUsername = $userManager->findUserBy(array('username' => $form["userName"]->getData()));
                    $usuarioPorEmail = $userManager->findUserBy(array('email' => $form["novios"]["eMail"]->getData()));
                    $usuariaPorEmail = $userManager->findUserBy(array('email' => $form["novias"]["eMail"]->getData()));

                    if($usuarioPorUsername || $usuarioPorEmail || $usuariaPorEmail)
                    {
                        $this->get('session')->getFlashBag()->add('notice', 'El usuario y/o el correo ya está ocupado');
                        return $this->render('NWUserBundle:User:registronovios.html.twig', array(
                            'form' => $form->createView(),
                        ));
                    }

                    /* // Generar alerta
                    if($usuarioPorUsername || $usuarioPorEmail)
                    {
                        $ruta = $this->getRefererRoute();
                        $locale = $request->get('_locale');
                        $url = $this->get('router')->generate($ruta, array('_locale' => $locale));
                        $this->get('session')->getFlashBag()->add('notice', 'El usuario y/o el correo del novio ya está ocupado');

                        return $this->redirect($url);
                    }*/

                    // Agregando Usuario y sus datos
                    $user = $userManager->createUser();

                    $user->setUsername($form["userName"]->getData());
                    $user->setEmail($form["novias"]["eMail"]->getData());
                    $user->setPlainPassword($form["userPass"]->getData());
                    $user->addRole('ROLE_NOVIO');
                    $user->setSaldo(0);
                    $user->setEnabled(true);

                    // Agregando Estado de la novia
                    $estadoNovia=$this->getDoctrine()->getRepository('NWPrincipalBundle:Estados')->find($form["novias"]["estado"]->getData());
                    $estadoNovia->addNovia($novias);
                    $novias->setEstados($estadoNovia);

                    // Agregando Estado del novio
                    $estadoNovio=$this->getDoctrine()->getRepository('NWPrincipalBundle:Estados')->find($form["novios"]["estado"]->getData());
                    $estadoNovio->addNovio($novios);
                    $novios->setEstados($estadoNovio);

                    // Agregando Usuario a los novios
                    $novias->setUser($user);
                    $novios->setUser($user);

                    // Agregando Novia al Novio
                    $novios->setNovia($novias);

                    // Agregando objeto boda a los novios
                    $boda = new Bodas();
                    $boda->setUser($user);
                    $boda->setCeremonia('');
                    $boda->setCeremoniaDireccion('');
                    $boda->setRecepcion('');
                    $boda->setRecepcionDireccion('');
                    $boda->setFechaBoda(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'));

                    // Persistiendo los datos en la base de datos
                    $em->persist($user);
                    $em->persist($novias);
                    $em->persist($novios);
                    $em->persist($boda);
                    $em->flush();

                    // Enviar correo a los novios por el registro exitoso
                    // Novio
                    $message = \Swift_Message::newInstance()
                    ->setSubject("Te registraste con éxito en NewlyWishes.com")
                    ->setFrom("info@newlywishes.com")
                    ->setTo($novios->getEMail())
                    ->setContentType("text/html")
                    ->setBody(
                        $this->renderView(
                            'NWUserBundle:User:correoRegistroExitoso.html.twig', array(
                                'user' => $user,
                                'contrasena' => $form["userPass"]->getData()
                            )
                        )
                    );
                    $this->get('mailer')->send($message);
                    // Novia
                    $message = \Swift_Message::newInstance()
                    ->setSubject("Te registraste con éxito en NewlyWishes.com")
                    ->setFrom("info@newlywishes.com")
                    ->setTo($novias->getEMail())
                    ->setContentType("text/html")
                    ->setBody(
                        $this->renderView(
                            'NWUserBundle:User:correoRegistroExitoso.html.twig', array(
                                'user' => $user,
                                'contrasena' => $form["userPass"]->getData()
                            )
                        )
                    );
                    $this->get('mailer')->send($message);

                    // El registro del formulario fue exitoso y se muestra mensaje de felicitación
                    return $this->redirect($this->generateUrl('nw_user_registro_exitoso'));
                }
            }
        }

        // Si no se ha ocupado el formulario (o contiene errores) se le muestra al usuario
        return $this->render('NWUserBundle:User:registronovios.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function registroproveedoresAction(Request $request)
    {
        // crea un nuevo registro y le asigna algunos datos
        $registro = new registroproveedores();
        $registro->setTipoPersona('');
        $registro->setNombreRazon('');
        $registro->setApellidoPaterno('');
        $registro->setApellidoMaterno('');
        $registro->setNombreComercial('');
        $registro->setRfc('');
        $registro->setEmail('');
        $registro->setLada('');
        $registro->setTelefono('');
        $registro->setCelular('');
        $registro->setDireccion('');
        $registro->setEstado('');
        $registro->setCiudad('');
        $registro->setCp('');
        $registro->setPlan('None');
 
        $form = $this->createFormBuilder($registro)
            ->add('tipoPersona', 'choice', array('choices' => 
                array('fisica' => 'Persona Física', 'moral' => 'Persona Moral'),
                'multiple' => false, 'expanded' => true, 'required' => true, 'empty_data'  => null))
            ->add('nombreRazon', 'text')
            ->add('apellidoPaterno', 'text', array('required' => false))
            ->add('apellidoMaterno', 'text', array('required' => false))
            ->add('nombreComercial', 'text', array('required' => false))
            ->add('rfc', 'text')
            ->add('email', 'email')
            ->add('lada', 'text', array('max_length' => 3))
            ->add('telefono', 'text', array('max_length' => 8))
            ->add('celular', 'text', array('max_length' => 10))
            ->add('direccion', 'text')
            ->add('pais', 'choice', array('choices' => array(
                    'MX'   => 'México',
                    ), 'mapped' => false, 'multiple'  => false,))
            ->add('estado', 'choice', array('choices' => array(
                     ''    => 'Estado',
                     '1'   => 'Aguascalientes',
                     '2'   => 'Baja California',
                     '3'   => 'Baja California Sur',
                     '4'   => 'Campeche',
                     '5'   => 'Chiapas',
                     '6'   => 'Chihuahua',
                     '7'   => 'Coahuila',
                     '8'   => 'Colima',
                     '9'   => 'Distrito Federal',
                     '10'   => 'Durango',
                     '11'   => 'Estado de México',
                     '12'   => 'Guanajuato',
                     '13'   => 'Guerrero',
                     '14'   => 'Hidalgo',
                     '15'   => 'Jalisco',
                     '16'   => 'Michoacán',
                     '17'   => 'Morelos',
                     '18'   => 'Nayarit',
                     '19'   => 'Nuevo León',
                     '20'   => 'Oaxaca',
                     '21'   => 'Puebla',
                     '22'   => 'Querétaro',
                     '23'   => 'Quintana Roo',
                     '24'   => 'San Luis Potosí',
                     '25'   => 'Sinaloa',
                     '26'   => 'Sonora',
                     '27'   => 'Tabasco',
                     '28'   => 'Tamaulipas',
                     '29'   => 'Tlaxcala',
                     '30'   => 'Veracruz',
                     '31'   => 'Yucatán',
                     '32'   => 'Zacatecas',
                    ), 'multiple'  => false,))
            ->add('ciudad', 'text', array('required' => false))
            ->add('cp', 'text', array('max_length' => 5))
            ->add('userName', 'text', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()))
            ->add('userPass', 'password', array('mapped' => false, 'required'  => true, 'constraints' => array(new NotBlank(), new Length(array('min' => 8)))))
            ->add('terminosCondiciones', 'checkbox', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()))
            ->add('terminosPrivacidad', 'checkbox', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()))
            /*->add('plan', 'choice', array('choices' => 
                array(
                    'anuncioEspecial' => 'Anuncio Especial',
                    'anuncioPlus' => 'Anuncio Plus',
                    'basico' => 'Básico',
                    'estandar' => 'Estándar',
                    'plus' => 'Plus'
                ),
                'multiple' => false, 'expanded' => true, 'required' => true, 'empty_data'  => null))
            */
            ->add('plan', 'hidden')
            ->add('Aceptar', 'submit')
            ->getForm();

        $form->handleRequest($request);
 
        if ($form->isValid()) {

            // Recuperando datos del formulario
            $proveedor = $form->getData();

            // Agregando Usuario
            $userManager = $this->get('fos_user.user_manager'); 
            $user = $userManager->createUser(); 

            $user->setUsername($form["userName"]->getData());
            $user->setEmail($form["email"]->getData());
            $user->setPlainPassword($form["userPass"]->getData());
            $user->setEnabled(true);
            $user->setSaldo(0);

            // Agregando rol de plan de proveedor
            switch ($form["plan"]->getData()) {
                case 'anuncioEspecial':
                    $user->addRole('ROLE_PROVEEDOR_ANUNCIO');
                    break;
                case 'anuncioPlus':
                    $user->addRole('ROLE_PROVEEDOR_ANUNCIO');
                    break;
                case 'basico':
                    $user->addRole('ROLE_PROVEEDOR_BASICO');
                    break;
                case 'estandar':
                    $user->addRole('ROLE_PROVEEDOR_ESTANDAR');
                    break;
                case 'plus':
                    $user->addRole('ROLE_PROVEEDOR_PLUS');
                    break;
                default:
                    $user->addRole('ROLE_PROVEEDOR');
                    break;
            }

            // Agregando Apellidos invisibles al proveedor si se trata de persona moral o si no existen apellidos
            if($form["tipoPersona"]->getData() == "moral")
            {
                $proveedor->setApellidoPaterno(" ");
                $proveedor->setApellidoMaterno(" ");
            }
            if(!$form["apellidoPaterno"]->getData())
            {
                $proveedor->setApellidoPaterno(" ");
            }
            if(!$form["apellidoMaterno"]->getData())
            {
                $proveedor->setApellidoMaterno(" ");
            }


            // Agregando Estado del proveedor
            $estado=$this->getDoctrine()->getRepository('NWPrincipalBundle:Estados')->find($form["estado"]->getData());
            $estado->addRegistroproveedore($proveedor);
            $proveedor->setEstados($estado);

            // Agregando Usuario a la tabla de registroproveedor
            $proveedor->setUser($user);
            
            $em = $this->getDoctrine()->getEntityManager(); 
            $em->persist($user);
            $em->persist($proveedor);
            $em->flush();
     
            /*return new Response(
                'proveedor creados con ID: '.$proveedor->getId().' y usuario con ID: '.$user->getId()
            );*/

            return $this->redirect($this->generateUrl('nw_user_registro_exitoso'));

        }
        else{
            return $this->render('NWUserBundle:User:registroproveedores.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

    public function registroReporteroAction(Request $request)
    {
        // Servicio con funciones para reportero
        $reporteroService = $this->get('reportero_service');

        // Nuevo objeto reportero y su formulario
        $reportero = new Reportero();
        $formReportero = $this->createForm(new ReporteroType(), $reportero);

        // Recuperando formulario de reportero
        $formReportero->handleRequest($request);
        if ($formReportero->isValid()) {

            // Registrar reportero mediante el servicio de reportero
            $reporteroService->registrarReportero($reportero, $formReportero['userName']->getData(), $formReportero['userPass']->getData());
            
            // Redirigir a la página de registro exitoso
            $redirect = $this->generateUrl('nw_user_registro_exitoso');
            return $this->redirect($redirect);
        }
        else{
            return new Response('Formulario valido');
        }

        // Generar página para el registro de nuevos reporteros
        return $this->render('NWUserBundle:User:registroreportero.html.twig', array(
            'formReportero' => $formReportero->createView(),
        ));
    }

    public function registroExitosoAction()
    {
        return $this->render('NWUserBundle:User:felicidades.html.twig');
    }

    // Obtiene la url de la página anterior
    public function getRefererRoute()
    {
        $request = $this->getRequest();

        //look for the referer route
        $referer = $request->headers->get('referer');
        $lastPath = substr($referer, strpos($referer, $request->getBaseUrl()));
        $lastPath = str_replace($request->getBaseUrl(), '', $lastPath);

        $matcher = $this->get('router')->getMatcher();
        $parameters = $matcher->match($lastPath);
        $route = $parameters['_route'];

        return $route;
    }
/*
    public function asignarRolesAction()
    {

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('username' => 'docsernovios'));
        //$user->addRole('ROLE_ONE');
        $user->setRoles(array('ROLE_USER1','ROLE_NOVIOS'));
        $userManager->updateUser($user);

        return new Response('Nuevo rol asignado correctamente al usuario '.$user->getId());
    }


	public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
 
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        }
        else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'NWUserBundle::login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );

    }

    public function newuserAction()
	{
		$factory = $this->get('security.encoder_factory');
		$usuario = new usuario();
		 
		$encoder = $factory->getEncoder($usuario);
		$password = $encoder->encodePassword('987410', ''); //, $usuario->getSalt()

	    $usuario->setUsername('Valerio2');
	    $usuario->setEmail('docser2@gmail.com');
	    $usuario->setPassword($password);
	 
	    $em = $this->getDoctrine()->getManager();
	    $em->persist($usuario);
	    $em->flush();
	 
	    return new Response('Usuario creado con id '.$usuario->getId());
	}

    public function asignRolesAction()
    {
        $roles= array('ROLE_ADMIN' => 'ROLE_ADMIN', 'ROLE_USER' => 'ROLE_USER');

        foreach($roles as $key => $value)
        {
            $user->addRole($value);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($usuario);
        $em->flush();

        return new Response('Usuario creado con id '.$user->getId());
    }*/
}
