<?php

namespace PG\PartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UsersController extends Controller
{
    public function registroAction()
    {
        return $this->render('PGPartyBundle:Users:registro.html.twig');
    }
}