<?php

namespace PG\PartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PG\PartyBundle\Form\ModificarCuentaType;

class AccountController extends Controller
{
    public function miCuentaAction()
    {	
    	// Generar formulario
    	$formModificarCuenta = $this->createForm(new ModificarCuentaType);

        return $this->render('PGPartyBundle:Account:micuenta.html.twig', array(
        	'formModificarCuenta' => $formModificarCuenta->createView(),
        ));
    }

    public function modificarCuentaAction()
    {
        return new Response("");
    }
}