<?php

namespace NW\PrincipalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
//Temporal
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function PanelAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();

    	// Conseguir solicitudes
    	$solicitudesRepository = $em->getRepository('NWPrincipalBundle:SolicitudRetiro');
    	$solicitudesCollection = $solicitudesRepository->findAll();

        return $this->render('NWPrincipalBundle:Admin:solicitudesRetiro.html.twig', array(
        	'solicitudes' => $solicitudesCollection,
        ));
    }

    public function aceptarRetiroAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	// Conseguir solicitud
    	$solicitudesRepository = $em->getRepository('NWPrincipalBundle:SolicitudRetiro');
    	$solicitudObject = $solicitudesRepository->find($id);

    	// Conseguir usuario
    	$usuario = $solicitudObject->getUsuario();

    	// Quitar saldo en web al usuario y marcar la solicitud como realizada
    	$solicitudObject->setRealizado(true);
    	$saldoViejo = $usuario->getSaldo();
    	$saldoNuevo = $saldoViejo - $solicitudObject->getAmount();
    	$usuario->setSaldo($saldoNuevo);
    	
    	// Persistir cambios
    	$em->persist($usuario);
    	$em->persist($solicitudObject);
    	$em->flush();

    	$respuesta = 'La solicitud (ID: '.$solicitudObject->getId().') ha sido aplicada al usuario '.$usuario->getUsername().', su saldo anterior era $'.$saldoViejo.' y el actual es $'.$saldoNuevo.' (se le debió haber depositado $'.$solicitudObject->getAmount().' a su cuenta paypal '.$solicitudObject->getCuentaPaypal().')';

    	// Se manda un mensaje de travesura realizada
        $this->get('session')->getFlashBag()->set(
            'notice',
            $respuesta
        );

        // Se manda correo a los novios de que su solicitud de retiro ha sido aceptada
        $message = \Swift_Message::newInstance()
        ->setSubject("Tu solicitud de retiro ha sido aceptada en NewlyWishes.com")
        ->setFrom("info@newlywishes.com")
        ->setTo($usuario->getNovios()->getEMail())
        ->setContentType("text/html")
        ->setBody(
            $this->renderView(
                'NWPrincipalBundle:Admin:solicitudRetiroAceptada.html.twig', array(
                    'id' => $solicitudObject->getId(),
                    'saldoViejo' => $saldoViejo,
                    'saldoNuevo' => $saldoNuevo,
                    'amount' => $solicitudObject->getAmount(),
                    'cuentaPaypal' => $solicitudObject->getCuentaPaypal(),
                )
            )
        );
        $this->get('mailer')->send($message);
        $message = \Swift_Message::newInstance()
        ->setSubject("Tu solicitud de retiro ha sido aceptada en NewlyWishes.com")
        ->setFrom("info@newlywishes.com")
        ->setTo($usuario->getNovias()->getEMail())
        ->setContentType("text/html")
        ->setBody(
            $this->renderView(
                'NWPrincipalBundle:Admin:solicitudRetiroAceptada.html.twig', array(
                    'id' => $solicitudObject->getId(),
                    'saldoViejo' => $saldoViejo,
                    'saldoNuevo' => $saldoNuevo,
                    'amount' => $solicitudObject->getAmount(),
                    'cuentaPaypal' => $solicitudObject->getCuentaPaypal(),
                )
            )
        );
        $this->get('mailer')->send($message);

        return $this->redirect($this->generateURL('nw_principal_admin_panel'));
    }

    public function denegarRetiroAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	// Conseguir solicitud y eliminarla
    	$solicitudesRepository = $em->getRepository('NWPrincipalBundle:SolicitudRetiro');
    	$solicitudObject = $solicitudesRepository->find($id);
    	$em->remove($solicitudObject);
    	$em->flush();

        return $this->redirect($this->generateURL('nw_principal_admin_panel'));
    }
}
