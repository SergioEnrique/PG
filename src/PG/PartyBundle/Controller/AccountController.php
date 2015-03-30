<?php

namespace PG\PartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use PG\PartyBundle\Entity\BucketGift;

use PG\PartyBundle\Form\ModificarCuentaType;
use PG\PartyBundle\Form\BucketGiftType;

class AccountController extends Controller
{
    public function miCuentaAction(Request $request)
    {	
    	// Manejador de entidades
    	$em = $this->getDoctrine()->getManager();

    	// Cargar datos del usuario
    	$user = $this->getUser();

    	// Generar formulario para Modificar la cuenta
    	$formModificarCuenta = $this->createForm(new ModificarCuentaType, $user);

        // Cargar todos los BucketGifts del usuario
        $bucketGifts = $user->getBucketGift();

        // Crear nuevo BucketGift
        $bucketGift = new BucketGift;

        // Asignar usuario al nuevo BucketGift
        $bucketGift->setUser($user);

        // Generar formulario para nuevo BucketGift
        $formBucketGift = $this->createForm(new BucketGiftType, $bucketGift);

    	// Recuperando formularios
    	if('POST' === $request->getMethod()) {
    	
    	    // Formulario de Modificar datos de Usuario
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
            // Formulario de Nuevo BucketGift
            else if ($request->request->has($formBucketGift->getName())) {
                $formBucketGift->handleRequest($request);
                if($formBucketGift->isValid())
                {
                    $ymd = \DateTime::createFromFormat('d/m/Y', $formBucketGift["date"]->getData())->format('Y-m-d');

                    $bucketGift->setFecha(new \DateTime($ymd));
                    $em->persist($bucketGift);
                    $em->flush();

                    $bucketGift->setActive();

                    // Se borra el formulario para que puedan añadir nuevos BucketGifts
                    $bucketGift = new BucketGift;
                    $bucketGift->setUser($user);
                    $formBucketGift = $this->createForm(new BucketGiftType, $bucketGift);
                }
            }
    	}

        return $this->render('PGPartyBundle:Account:micuenta.html.twig', array(
        	'formModificarCuenta' => $formModificarCuenta->createView(),
            'formBucketGift' => $formBucketGift->createView(),
            'bucketGifts' => $bucketGifts,
        ));
    }

    public function bucketGiftDeleteAction($id) // Controlador que borra un BucketGift según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $bucketGift = $em->getRepository('PGPartyBundle:BucketGift')->find($id);

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || $this->getUser()->getId() != $bucketGift->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }
        else{

            $em->remove($bucketGift);
            $em->flush();

            return $this->redirect($this->generateUrl('pg_party_miCuenta'));
        }
    }
}