<?php

namespace NW\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('NWPaymentBundle:Default:index.html.twig');
    }
}