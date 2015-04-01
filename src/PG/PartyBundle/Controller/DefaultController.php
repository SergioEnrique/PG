<?php

namespace PG\PartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$request = $this->getRequest();
        $session = $request->getSession();
 
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        }
        else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
            : null;

        return $this->render(
            'PGPartyBundle:Default:index.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                'csrf_token' => $csrfToken,
            )
        );
    }

    public function partyAction($id)
    {
        // Obtener el partygift según su ID
        $em = $this->getDoctrine()->getManager();
        $partyGiftRepo = $em->getRepository('NWPrincipalBundle:MesaRegalos');
        $partyGift = $partyGiftRepo->find($id);

        // Buscar el nombre del creador del partygift
        $nombre = $partyGift->getBucketGift()->getUser()->getNombre();

        return $this->render('PGPartyBundle:Default:party.html.twig', array(
            'partyGift' => $partyGift,
            'nombre' => $nombre,
        ));
    }
}