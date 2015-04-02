<?php

namespace PG\PartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

use Symfony\Component\HttpFoundation\Request;

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

    public function busquedaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Si se encontraron datos y es peticion ajax
        if($this->getRequest()->isXmlHttpRequest())
        {
            $usuariosRepo = $em->getRepository('NWUserBundle:User');
            $bucketsRepo = $em->getRepository('PGPartyBundle:BucketGift');

            $query = $bucketsRepo->createQueryBuilder('b') // Tabla de BucketGifts
                ->innerJoin('b.user', 'u'); // Tabla de Usuarios

            if($request->request->get('nombre'))
            {
                $query->orWhere('u.nombre LIKE :nombre');
                $parametros['nombre'] = '%'.$request->request->get('nombre').'%';
            }
            if($request->request->get('apellidos'))
            {
                $query->orWhere('u.apellidos LIKE :apellidos');
                $parametros['apellidos'] = '%'.$request->request->get('apellidos').'%';
            }
            if($request->request->get('evento'))
            {
                $query->orWhere('b.titulo LIKE :evento');
                $parametros['evento'] = '%'.$request->request->get('evento').'%';
            }
            if($request->request->get('bucketid'))
            {
                $query->orWhere('b.id = :bucketid');
                $parametros['bucketid'] = $request->request->get('bucketid');
            }

            $query->setParameters($parametros);
            $result = $query->getQuery()->getResult();

            $resultados=array();
            foreach ($result as $key => $value) {
                $resultados[$key]['nombre'] = $result[$key]->getUser()->getNombre();
                $resultados[$key]['apellidos'] = $result[$key]->getUser()->getApellidos();
                $resultados[$key]['evento'] = $result[$key]->getTitulo();
                $resultados[$key]['bucketid'] = $result[$key]->getId();
                $resultados[$key]['fecha'] = $result[$key]->getFechaformateada();
                $resultados[$key]['partygifts'] = array();
                foreach ($result[$key]->getMesaRegalos()->toArray() as $index => $partygift) {
                    $resultados[$key]['partygifts'][$index] = $partygift->getAsArray();
                }
                
            }

            $return["result"] = $resultados;

            // La petición funciona
            $return["responseCode"] = 200;
            $responseCode = 200;
            $return = json_encode($return);
        }
        else{
            // La petición no funciona
            $return["responseCode"] = 500;
            $responseCode = 500;
            $return = json_encode($return);
        }

        // Regresa el resultado en JSON
        return new Response($return, $responseCode, array('Content-Type'=>'application/json'));
    }
}