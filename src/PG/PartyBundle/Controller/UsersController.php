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
            $user = $userManager->createUser(); 

            $user->setUsername($formRegistroPary["email"]->getData());
            $user->setEmail($formRegistroPary["email"]->getData());
            $user->setPlainPassword($formRegistroPary["userPass"]->getData());
            $user->setMoneda($formRegistroPary["moneda"]->getData());
            $user->setEnabled(true);
            $user->setSaldo(0);

            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'secure_area', array('ROLE_USER'));
            $this->get('security.context')->setToken($token);
            $this->get( 'event_dispatcher' )->dispatch(AuthenticationEvents::AUTHENTICATION_SUCCESS, new AuthenticationEvent( $token ) );

            // Operaciones de registro exitoso
            return $this->render('PGPartyBundle:Users:registroExitoso.html.twig');

	   }

        return $this->render('PGPartyBundle:Users:registro.html.twig', array(
        	'formRegistroPary' => $formRegistroPary->createView(),
            'emailpasado' => $request->query->get('email'),
        ));
    }
}