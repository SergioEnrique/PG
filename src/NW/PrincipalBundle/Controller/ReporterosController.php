<?php

namespace NW\PrincipalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use NW\PrincipalBundle\Entity\ArticuloReportero;

use NW\PrincipalBundle\Form\Type\BusquedaArticulosType;
use NW\PrincipalBundle\Form\ArticuloReporteroType;
use NW\UserBundle\Form\Type\ReporteroType;

class ReporterosController extends Controller
{
	public function micuentaAction(Request $request)
    {
    	// Servicio con funciones para reportero
    	$reporteroService = $this->get('reportero_service');

    	// Formulario de edición de reportero
    	$reportero = $reporteroService->getReportero($this->getUser());
    	$formReportero = $this->createForm(new ReporteroType(), $reportero);

    	// Formulario de cambio de contraseña
        $formPassword = $this->createFormBuilder()
            ->add('oldPass', 'password')
            ->add('newPass', 'password')
            ->add('Cambiar', 'submit')
            ->getForm();

        // Recuperando formularios
        if('POST' === $request->getMethod()) {
            // Formulario de cambio de contraseña
            if($request->request->has($formPassword->getName())) {
                $formPassword->handleRequest($request);
                if($formPassword->isValid())
                {
                	$reporteroService->changePass($this->getUser(), $formPassword["oldPass"]->getData(), $formPassword["newPass"]->getData());
                }
            }
            // Formulario de actualización de datos
            else if($request->request->has($formReportero->getName())) {
                $formReportero->handleRequest($request);
                if($formReportero->isValid())
                {
                	$reporteroService->actualizarReportero($reportero);
                }
            }
        }

        // Obtener datos de reportero según el usuario en formato para mostrar en plantilla
    	$reporteroArray = $reporteroService->getReporteroArray($this->getUser());

    	return $this->render('NWPrincipalBundle:Reporteros:micuenta.html.twig', array(
    		'reportero' => $reporteroArray,
    		'formPassword' => $formPassword->createView(),
    		'formReportero' => $formReportero->createView(),
    	));
    }

    public function misarticulosAction(Request $request)
    {
    	// Servicio con funciones para reportero
    	$reporteroService = $this->get('reportero_service');

    	// Objeto Reportero
    	$reportero = $reporteroService->getReportero($this->getUser());

    	// Formulario para nuevo artículo
    	$articulo = new ArticuloReportero();
    	$articulo->setReportero($reportero);
    	$formArticulo = $this->createForm(new ArticuloReporteroType(), $articulo, array('categorias' => $reporteroService->getCategorias()));

    	// Recuperando formularios
        if('POST' === $request->getMethod()) {
            // Formulario de carga de artículo
            if($request->request->has($formArticulo->getName())) {
                $formArticulo->handleRequest($request);
                if($formArticulo->isValid())
                {
                	$reporteroService->cargarArticulo($articulo);
                }
            }
        }

    	// Obtener datos de reportero según el usuario en formato para mostrar en plantilla
    	$reporteroArray = $reporteroService->getReporteroArray($this->getUser());

    	// Obtener todos los artículos del reportero
    	$articulos = $reporteroService->getArticulos($reportero);

    	return $this->render('NWPrincipalBundle:Reporteros:misarticulos.html.twig', array(
    		'reportero' => $reporteroArray,
    		'formArticulo' => $formArticulo->createView(),
    		'articulos' => $articulos,
    	));
    }

    public function enviarArticuloAction($id)
    {
    	// Servicio con funciones para reportero
    	$reporteroService = $this->get('reportero_service');

    	$reporteroService->enviarArticulo($id);

    	$redirect = $this->generateUrl('nw_principal_reporteros_misarticulos');
    	return $this->redirect($redirect);
    }

    public function eliminarArticuloAction($id)
    {
    	// Servicio con funciones para reportero
    	$reporteroService = $this->get('reportero_service');

    	$reporteroService->eliminarArticulo($id);

    	$redirect = $this->generateUrl('nw_principal_reporteros_misarticulos');
    	return $this->redirect($redirect);
    }

    public function miestadodecuentaAction()
    {
    	// Servicio con funciones para reportero
    	$reporteroService = $this->get('reportero_service');

    	// Obtener datos de reportero según el usuario en formato para mostrar en plantilla
    	$reporteroArray = $reporteroService->getReporteroArray($this->getUser());

    	return $this->render('NWPrincipalBundle:Reporteros:miestadodecuenta.html.twig', array(
    		'reportero' => $reporteroArray,
    	));
    }

    public function miinformacionbancariaAction()
    {
    	// Servicio con funciones para reportero
    	$reporteroService = $this->get('reportero_service');

    	// Obtener datos de reportero según el usuario en formato para mostrar en plantilla
    	$reporteroArray = $reporteroService->getReporteroArray($this->getUser());

    	return $this->render('NWPrincipalBundle:Reporteros:miinformacionbancaria.html.twig', array(
    		'reportero' => $reporteroArray,
    	));
    }
}