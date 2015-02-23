<?php

namespace PG\PartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PGPartyBundle:Default:index.html.twig');
    }

    public function partyAction()
    {
        return $this->render('PGPartyBundle:Default:party.html.twig');
    }
}