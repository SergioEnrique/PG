<?php

namespace PG\PartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
    public function miCuentaAction()
    {
        return $this->render('PGPartyBundle:Account:micuenta.html.twig');
    }
}