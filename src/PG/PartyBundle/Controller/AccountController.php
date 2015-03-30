<?php

namespace PG\PartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use PG\PartyBundle\Form\ModificarCuentaType;

class AccountController extends Controller
{
    public function miCuentaAction(Request $request)
    {	
    	// Manejador de entidades
    	$em = $this->getDoctrine()->getManager();

    	// Cargar datos del usuario
    	$user = $this->getUser();

    	// Generar formulario
    	$formModificarCuenta = $this->createForm(new ModificarCuentaType, $user);

    	// Recuperando formularios
    	if('POST' === $request->getMethod()) {
    	
    	    // Formulario 1
    	    if ($request->request->has($formModificarCuenta->getName())) {
                $formModificarCuenta->handleRequest($request);
                if($formModificarCuenta->isValid())
                {
                    // Si el usuario indicó una nueva contraseña
                    if ($formModificarCuenta["newPass"]->getData() != NULL) {
                        // Codificando la contraseña escrita para después compararla con la original
                        $encoder_service = $this->get('security.encoder_factory');
                        $encoder = $encoder_service->getEncoder($user);
                        $encoder_pass = $encoder->encodePassword($formModificarCuenta["oldPass"]->getData(), $user->getSalt());

                        // Verificar que la contraseña escrita sea correcta
                        if($encoder_pass === $user->getPassword())
                        {
                            $user->setPlainPassword($formModificarCuenta["newPass"]->getData());
                        }
                        else
                        {
                            $this->get('session')->getFlashBag()->set(
                                'notice',
                                'La contraseña es incorrecta'
                            );
                        }
                    }
                    
                    // Se modifican los demás datos
                    $user->setNombre($user->getNombre());
                    $user->setApellidos($user->getApellidos());
                    $user->setMoneda($user->getMoneda());
                    $user->setUsername($user->getEmail());
                    $user->setEmail($user->getEmail());
                    $this->get('fos_user.user_manager')->updateUser($user, false);

                    $em->flush();

                }
    		}
    	}

        return $this->render('PGPartyBundle:Account:micuenta.html.twig', array(
        	'formModificarCuenta' => $formModificarCuenta->createView(),
        ));
    }
}