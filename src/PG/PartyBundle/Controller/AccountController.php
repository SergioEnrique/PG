<?php

namespace PG\PartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use PG\PartyBundle\Entity\BucketGift;
use NW\PrincipalBundle\Entity\MesaRegalos;
use NW\PrincipalBundle\Entity\SolicitudRetiro;

use PG\PartyBundle\Form\ModificarCuentaType;
use PG\PartyBundle\Form\BucketGiftType;
use NW\PrincipalBundle\Form\MesaRegalosType;
use NW\PrincipalBundle\Form\SolicitudRetiroType;

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

        // Crear nuevo PartyGift independiente
        $partyGift = new MesaRegalos;
        $partyGift->setCantidad(1);
        $partyGift->setHorcruxes(1);

        // Crear nuevo formulario de PartyGift
        $formPartyGift = $this->createForm(new MesaRegalosType, $partyGift);

        // Formulario de solicitud de retiro estableciendo el maximo que puede retirar
        $nuevaSolicitudRetiro = new SolicitudRetiro();
        $nuevaSolicitudRetiro->setMaximoRetiro($user->getSaldo());
        $formSolicitudRetiro = $this->createForm(new SolicitudRetiroType(), $nuevaSolicitudRetiro);

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
            // Formulario de Nuevo PartyGift
            else if ($request->request->has($formPartyGift->getName())) {
                $formPartyGift->handleRequest($request);
                if($formPartyGift->isValid())
                {
                    $bucketGiftId = $formPartyGift['bucketGiftId']->getData();
                    $bucketGiftEntity = $em->getRepository('PGPartyBundle:BucketGift');
                    $miBucketGift = $bucketGiftEntity->find($bucketGiftId);

                    $partyGift->setBucketGift($miBucketGift);
                    $partyGift->setHorcruxesPagados(0);

                    $em->persist($partyGift);
                    $em->flush();

                    // Se marca el bucketgift como activo
                    $bucketGifts->get($bucketGifts->indexOf($miBucketGift))->setActive();

                    // Se borra el formulario para hacer nuevo partygift
                    $partyGift = new MesaRegalos;
                    $partyGift->setCantidad(1);
                    $partyGift->setHorcruxes(1);
                    $formPartyGift = $this->createForm(new MesaRegalosType, $partyGift);
                }
            }
            // Formulario de solicitud de retiro
            else if ($request->request->has($formSolicitudRetiro->getName())) {
                // handle form de solicitud de retiro
                $formSolicitudRetiro->handleRequest($request);
                if ($formSolicitudRetiro->isValid()) {
                    // Mandar solicitud de retiro solo si no tiene solicitudes anteriores sin aceptar
                    $solicitudesRepository = $em->getRepository('NWPrincipalBundle:SolicitudRetiro');
                    $solicitudSinAprobarObject = $solicitudesRepository->findOneBy(array('realizado' => false));

                    if (!is_object($solicitudSinAprobarObject)) {
                        // Aqui pasa todo
                        $nuevaSolicitudRetiro->setUsuario($user);
                        $nuevaSolicitudRetiro->setFecha(new \DateTime());
                        $nuevaSolicitudRetiro->setRealizado(false);

                        $em->persist($nuevaSolicitudRetiro);
                        $em->flush();

                        // Enviar correo al usuario de que se solicitó un retiro
                        $message = \Swift_Message::newInstance()
                        ->setSubject("Solicitud de Retiro en PartyGift")
                        ->setFrom("info@newlywishes.com")
                        ->setTo($user->getEmail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'PGPartyBundle:Users:correoSolicitudRetiro.html.twig', array(
                                    'vacio' => "vacio",
                                )
                            )
                        );
                        $this->get('mailer')->send($message);

                        // PartyGift finanzas o admin
                        $message = \Swift_Message::newInstance()
                        ->setSubject("Solicitud de Retiro en NewlyWishes.com")
                        ->setFrom("info@newlywishes.com")
                        ->setTo("docser@gmail.com")
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'PGPartyBundle:Users:correoSolicitudRetiro.html.twig', array(
                                    'vacio' => "vacio",
                                )
                            )
                        );
                        $this->get('mailer')->send($message);

                        // Se manda un mensaje de travesura realizada
                        $this->get('session')->getFlashBag()->set(
                            'notice',
                            'Se ha enviado la solicitud para retirar su dinero en la cuenta de paypal indicada. Por favor espere a que sea aprobada.'
                        );
                    }
                    else
                    {
                        // Se manda un mensaje de travesura no realizada
                        $this->get('session')->getFlashBag()->set(
                            'notice',
                            'Ya tienes una solicitud de retiro en espera, espera a que sea procesada antes de mandar otra.'
                        );   
                    }
                    
                }
            }
    	}

        return $this->render('PGPartyBundle:Account:micuenta.html.twig', array(
        	'formModificarCuenta' => $formModificarCuenta->createView(),
            'formBucketGift' => $formBucketGift->createView(),
            'formPartyGift' => $formPartyGift->createView(),
            'formSolicitudRetiro' => $formSolicitudRetiro->createView(),
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

            // Remover todos los partygifts
            $partyGiftsArray = $bucketGift->getMesaRegalos()->toArray();
            foreach ($partyGiftsArray as $key => $partyGift) {
                $em->remove($partyGift);
            }

            // Remover el bucketgift
            $em->remove($bucketGift);
            $em->flush();

            return $this->redirect($this->generateUrl('pg_party_miCuenta'));
        }
    }

    public function partyGiftDeleteAction($id) // Controlador que borra un PartyGift según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $partyGift = $em->getRepository('NWPrincipalBundle:MesaRegalos')->find($id);

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || $this->getUser()->getId() != $partyGift->getBucketGift()->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }
        else{

            $em->remove($partyGift);
            $em->flush();

            return $this->redirect($this->generateUrl('pg_party_miCuenta'));
        }
    }
}