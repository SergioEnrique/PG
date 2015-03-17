<?php

namespace NW\PrincipalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use NW\PrincipalBundle\Form\Type\EdicionNoviosType;
use NW\PrincipalBundle\Form\Type\ChecklistType;
use NW\PrincipalBundle\Form\Type\ListaInvitadosType;
use NW\PrincipalBundle\Form\Type\DatosBodaType;
use NW\PrincipalBundle\Form\Type\PadrinosType;
use NW\PrincipalBundle\Form\Type\NotasType;
use NW\PrincipalBundle\Form\Type\RegaloType;
use NW\PrincipalBundle\Form\Type\DiaBodaType;
use NW\PrincipalBundle\Form\Type\BusquedaArticulosType;
use NW\PrincipalBundle\Form\TareaCalendarioType;
use NW\PrincipalBundle\Form\SolicitudRetiroType;

use NW\PrincipalBundle\Entity\Checklist;
use NW\PrincipalBundle\Entity\ListaInvitados;
use NW\PrincipalBundle\Entity\Bodas;
use NW\PrincipalBundle\Entity\Padrinos;
use NW\PrincipalBundle\Entity\Notas;
use NW\PrincipalBundle\Entity\MesaRegalos;
use NW\PrincipalBundle\Entity\CatRegalos;
use NW\PrincipalBundle\Entity\CategoriaCalendario;
use NW\PrincipalBundle\Entity\TareaCalendario;
use NW\PrincipalBundle\Entity\SolicitudRetiro;

use NW\UserBundle\Entity\Novias;
use NW\UserBundle\Entity\Novios;

class NoviosController extends Controller
{
    public function indexAction()
    {
        return $this->render('NWPrincipalBundle:Novios:index.html.twig');
    }

    public function dudasNoviosAction(Request $request)
    {
        // Recuperando formularios
        if('POST' === $request->getMethod()) {

            // Información del correo
            $user = $this->getUser();
            $asunto = $this->get('request')->request->get('asunto');
            $mensaje = $this->get('request')->request->get('mensaje');

            // Enviar correo a atencion@newlywishes.com con las dudas de los novios
            $message = \Swift_Message::newInstance()
            ->setSubject("Mensaje de usuario: ".$asunto)
            ->setFrom("info@newlywishes.com")
            ->setTo("atencion@newlywishes.com")
            ->setContentType("text/html")
            ->setBody(
                $this->renderView(
                    'NWPrincipalBundle:Novios:dudasNovios.html.twig', array(
                        'user' => $user,
                        'asunto' => $asunto,
                        'mensaje' => $mensaje
                    )
                )
            );
            $this->get('mailer')->send($message);

            // Se manda un mensaje de travesura realizada
            $this->get('session')->getFlashBag()->set(
                'notice',
                'Se ha recibido con éxito su mensaje, le responderemos por correo tan pronto como sea posible.'
            );
        }
        
        return $this->redirect($this->generateUrl('nw_principal_novios_nuestra-boda'));
    }
	
	public function nuestraBodaAction(Request $request)
    {
        // Manejador de Doctrine
        $em = $this->getDoctrine()->getManager();

        $user=$this->getUser();
        $novia=$user->getNovias();
        $novio=$user->getNovios();

        // Recuperar datos de la boda que ya existen (si es que existen)
        $formBodaData = $em->getRepository('NWPrincipalBundle:Bodas')->findOneByUsuarioId($user->getId());

        if(!$formBodaData)
        {
            $formBodaData = new Bodas();
            $formBodaData->setUser($user);
            $formBodaData->setCeremonia('');
            $formBodaData->setCeremoniaDireccion('');
            $formBodaData->setRecepcion('');
            $formBodaData->setRecepcionDireccion('');
        }
         
        // Generar Formularios   
        $formBoda = $this->createForm(new DatosBodaType(), $formBodaData);
        $formPadrinosData = new Padrinos();
        $formPadrinos = $this->createForm(new PadrinosType(), $formPadrinosData);
        $formNotasData = new Notas();
        $formNotas = $this->createForm(new NotasType(), $formNotasData);

        // Recuperando formularios
        if('POST' === $request->getMethod()) {

            // Formulario de datos de la boda
            if ($request->request->has($formBoda->getName())) {
                $formBoda->handleRequest($request);
                if($formBoda->isValid())
                {
                    // Recordar la fecha de la boda o setear en el 2000 si no hay fecha
                    if(!$formBodaData->hayFechaBoda())
                    {
                        $formBodaData->setFechaBoda(\DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00'));
                    }

                    $em->persist($formBodaData);
                    $em->flush();
                }
            }
            // Formulario de agregar padrino
            else if ($request->request->has($formPadrinos->getName())) {
                // Recuperando datos del formulario
                $formPadrinos->handleRequest($request);

                if($formPadrinos->isValid())
                {
                    $newPadrino=$formPadrinos->getData();
                    $newPadrino->setUser($user);

                    $em->persist($newPadrino);
                    $em->flush();

                    // Limpiar campos de formulario para agregar padrinos
                    $formPadrinosData = new Padrinos();
                    $formPadrinos = $this->createForm(new PadrinosType(), $formPadrinosData);
                }
            }
            // Formulario de agregar nota
            else if ($request->request->has($formNotas->getName())) {
                // Recuperando datos del formulario
                $formNotas->handleRequest($request);

                if($formNotas->isValid())
                {
                    $newNota=$formNotas->getData();
                    $newNota->setUser($user);

                    $em->persist($newNota);
                    $em->flush();

                    // Limpiar campos de formulario para agregar notas
                    $formNotasData = new Notas();
                    $formNotas = $this->createForm(new NotasType(), $formNotasData);
                }
            }
        }

        // Obteniendo la lista de padrinos y notas en sus respectivos arreglos de objetos
        $padrinos = $em->getRepository('NWPrincipalBundle:Padrinos')->findBy(array('usuarioId' => $user->getId()));
        $notas = $em->getRepository('NWPrincipalBundle:Notas')->findBy(array('usuarioId' => $user->getId()));

        // Convirtiendo los resultados de padrinos en arrays
        foreach($padrinos as $index=>$value)
        {
            $objetoenArray=$padrinos[$index]->getValues();
            $padrinos[$index]=$objetoenArray;
        }

        // Convirtiendo los resultados de notas en arrays
        foreach($notas as $index=>$value)
        {
            $objetoenArray=$notas[$index]->getValues();
            $notas[$index]=$objetoenArray;
        }

        return $this->render('NWPrincipalBundle:Novios:nuestra-boda.html.twig', array(
            'formBoda' => $formBoda->createView(),
            'formPadrinos' => $formPadrinos->createView(),
            'formNotas' => $formNotas->createView(),
            'hayFechaBoda' => $formBodaData->hayFechaBoda(),
            'contadorFechaBoda' => $formBodaData->contadorFechaBoda(),
            'novia' => $novia->getNombre(),
            'novio' => $novio->getNombre(),
            'padrinos' => $padrinos,
            'notas' => $notas,
            'fechaBodaFormat' => $formBodaData->fechaBodaFormat(),
            'saldo' => $user->getSaldoFormat(),
        ));
    }

    public function CambiarFechaBodaAction($fecha)
    {
        $em = $this->getDoctrine()->getManager();

        $fecha = new \DateTime($fecha);

        $user = $this->getUser();
        $bodaEntity = $em->getRepository('NWPrincipalBundle:Bodas');
        $boda = $bodaEntity->findOneBy(array('usuarioId' => $user->getId()));

        $boda->setFechaBoda($fecha);

        $em->persist($boda);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_novios_nuestro-calendario'));
    }

    public function PadrinoDeleteAction($id) // Controlador que borra un padrino según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $padrino = $em->getRepository('NWPrincipalBundle:Padrinos')->find($id);
        $em->remove($padrino);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_novios_nuestra-boda'));
    }

    public function NotaDeleteAction($id) // Controlador que borra una nota según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $nota = $em->getRepository('NWPrincipalBundle:Notas')->find($id);
        $em->remove($nota);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_novios_nuestra-boda'));
    }
	
	public function nuestroCalendarioAction($dia, $mes, $ano, Request $request)
    {
        // Manejador de Doctrine
        $em = $this->getDoctrine()->getManager();

        if($ano){
            $lookForDay = $ano.'-'.$mes.'-'.$dia;
            $lookForDay = \DateTime::createFromFormat('Y-m-d', $lookForDay);
        }
        else
        {
            $lookForDay = new \DateTime();
        }

        $user = $this->getUser();
        $novia = $user->getNovias();
        $novio = $user->getNovios();
        $BodaVieja = $em->getRepository('NWPrincipalBundle:Bodas')->findOneByUsuarioId($user->getId());

        // Obteniendo categorías de tareas de calendario
        $categoriaEntity = $em->getRepository('NWPrincipalBundle:CategoriaCalendario');
        $categorias = $categoriaEntity->findAll();

        // Convirtiendo los objetos de categorías en un arreglo de strings
        foreach ($categorias as $key => $categoria) {
            $categoriasArr[$categoria->getId()] = $categoria->getCategoria();
        }

        // Formulario de nueva tarea de Calendario
        $nuevaTarea = new TareaCalendario();
        $nuevaTarea->setUser($user);
        $formTareaCalendario = $this->createForm(new TareaCalendarioType(), $nuevaTarea, array('categorias' => $categoriasArr));

        // Recuperando formulario si es AJAX
        if($this->getRequest()->isXmlHttpRequest())
        {
            $formTareaCalendario->bind($request);
            if($formTareaCalendario->isValid())
            {
                // Persistir formulario a base de datos
                $categoria = $categoriaEntity->find($nuevaTarea->getCategoriaId());
                $nuevaTarea->setcategoria($categoria);

                if($ano){
                    $vencimiento = new \DateTime(date($ano.'-'.$mes.'-'.$dia.' '.$formTareaCalendario['hora']->getData().':'.$formTareaCalendario['minuto']->getData() ));
                }
                else
                {
                    $vencimiento = new \DateTime(date('Y-m-d'.' '.$formTareaCalendario['hora']->getData().':'.$formTareaCalendario['minuto']->getData() ));
                }

                $nuevaTarea->setVencimiento($vencimiento);
                $nuevaTarea->setHecho(false);

                if(is_null($nuevaTarea->getContactoNombre())){$nuevaTarea->setContactoNombre('');}
                if(is_null($nuevaTarea->getContactoEmail())){$nuevaTarea->setContactoEmail('');}
                if(is_null($nuevaTarea->getContactoDireccion())){$nuevaTarea->setContactoDireccion('');}
                if(is_null($nuevaTarea->getContactoTelefono())){$nuevaTarea->setContactoTelefono('');}

                $em->persist($nuevaTarea);
                $em->flush();

                // respuestas
                $responseCode = 200;
                $return["responseCode"] = $responseCode;
                $return["newID"] = $nuevaTarea->getId();
                $return["compromiso"] = $nuevaTarea->getCompromiso();
                $return["descripcion"] = $nuevaTarea->getDescripcion();
                $return["categoria"] = $categoria->getCategoria();
                $return["vencimiento"] = $vencimiento->format("H:i");

                $return["nombre"] = $nuevaTarea->getContactoNombre();
                $return["telefono"] = $nuevaTarea->getContactoTelefono();
                $return["email"] = $nuevaTarea->getContactoEmail();
                $return["direccion"] = $nuevaTarea->getContactoDireccion();

                // Se genera un nuevo objeto para el formulario
                $nuevaTarea = new TareaCalendario();
                $nuevaTarea->setUser($user);
                $formTareaCalendario = $this->createForm(new TareaCalendarioType(), $nuevaTarea, array('categorias' => $categoriasArr));                
            }
            else
            {
                // La petición no funciona
                $responseCode = 500;
                $return = array("responseCode" => $responseCode);
            }

            // Se devuelve al cliente el resultado de la operación ajax
            $return = json_encode($return);
            return new Response($return, $responseCode, array('Content-Type'=>'application/json'));
        }

        $tareasEntity = $em->getRepository('NWPrincipalBundle:TareaCalendario');
        $tareas = $tareasEntity->findBy(array('usuarioId' => $user->getId()));

        foreach ($tareas as $key => $tarea) {
            $tareas[$key] = $tarea->getValues();

            $fecha = $tareas[$key]['vencimientoDatetime'];
            $guiones = $fecha->format('Y-m-d');

            $fechaUnix = strtotime($guiones)-2592000;
            $fechaJavascript = new \DateTime();
            $fechaJavascript->setTimestamp($fechaUnix);

            $Y = $fecha->format('Y');
            $m = $fecha->format('n');
            $d = $fecha->format('j');
            $H = $fecha->format('G');
            $i = $fecha->format('i');

            if($m == 0)
            {
                $m = 12;
            }
            else
            {
                $m = $m-1;
            }

            $javascript = $Y.','.$m.','.$d.','.$H.','.$i;

            $tareas[$key]['guiones'] = $guiones;
            $tareas[$key]['javascript'] = $javascript;

            // Mostrar para el día seleccionado
            if($guiones == $lookForDay->format('Y-m-d'))
            {
                $tareas[$key]['show'] = true;
            }
            else
            {
                 $tareas[$key]['show'] = false;
            }
        }

        $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $arrayDias = array( 'Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado');
        $diaActual = $lookForDay->format('j')." de ".$arrayMeses[$lookForDay->format('n')-1]." de ".$lookForDay->format('Y');

        $diaActual2 = $lookForDay->format('Y').'-'.$lookForDay->format('n').'-'.$lookForDay->format('j');

        return $this->render('NWPrincipalBundle:Novios:nuestro-calendario.html.twig', array(
            'novia' => $novia->getNombre(),
            'novio' => $novio->getNombre(),
            'hayFechaBoda' => $BodaVieja->hayFechaBoda(),
            'contadorFechaBoda' => $BodaVieja->contadorFechaBoda(),
            'fechaBodaFormat' => $BodaVieja->fechaBodaFormat(),
            'formTareaCalendario' => $formTareaCalendario->createView(),
            'tareas' => $tareas,
            'diaActual' => $diaActual,
            'diaActual2' => $diaActual2,
            'saldo' => $user->getSaldoFormat(),
        ));
    }

    public function TareaCalendarioAgregarAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {

        }
    }

    public function TareaCalendarioDeleteAction($id) // Controlador que borra una tarea de calendario según el id pasado
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
           $em = $this->getDoctrine()->getManager();
            $tarea = $em->getRepository('NWPrincipalBundle:TareaCalendario')->find($id);
            $em->remove($tarea);
            $em->flush();
            return new Response("{\"eliminado\": true}");
        }
        else{
            return new Response("Acceso no permitido");
        }
    }

    public function TareaCalendarioCompletarAction($id) // Controlador que completa una tarea de calendario según el id pasado
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $tarea = $em->getRepository('NWPrincipalBundle:TareaCalendario')->find($id);
            $tarea->setHecho(true);
            $em->persist($tarea);
            $em->flush();
            return new Response("{\"completado\": true}");
        }
        else{
            return new Response("Acceso no permitido");
        }

    }

    public function TareaCalendarioDescompletarAction($id) // Controlador que descompleta una tarea de calendario según el id pasado
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $tarea = $em->getRepository('NWPrincipalBundle:TareaCalendario')->find($id);
            $tarea->setHecho(false);
            $em->persist($tarea);
            $em->flush();
            return new Response("{\"descompletado\": true}");
        }
        else{
            return new Response("Acceso no permitido");
        }
    }
	
	public function nuestroChecklistAction(Request $request)
    {
        // Manejador de Doctrine
        $em = $this->getDoctrine()->getManager();

        $user=$this->getUser();
        $novia=$user->getNovias();
        $novio=$user->getNovios();
        $BodaVieja = $em->getRepository('NWPrincipalBundle:Bodas')->findOneByUsuarioId($user->getId());

        $formAgregarData = new Checklist();
        $formAgregar = $this->createForm(new ChecklistType(), $formAgregarData);

        // Recuperando formularios
        if('POST' === $request->getMethod()) {
 
            // Formulario de agregar tarea
            if ($request->request->has($formAgregar->getName())) {
                // Recuperando datos del formulario de cambio de contraseña
                $formAgregar->handleRequest($request);

                if($formAgregar->isValid())
                {
                    $newTask=$formAgregar->getData();
                    $newTask->setUser($user);
                    $newTask->setStatus(false);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($newTask);
                    $em->flush();

                    // Limpiar formulario
                    $formAgregarData = new Checklist();
                    $formAgregar = $this->createForm(new ChecklistType(), $formAgregarData);

                }
            }
            
        }

        // Obteniendo la lista de tareas en un arreglo de objectos
        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository('NWPrincipalBundle:Checklist')->findBy(array('usuarioId' => $user->getId()));

        // Convirtiendo los resultados en arrays
        foreach($tasks as $index=>$value)
        {
            $objetoenArray=$tasks[$index]->getValues();
            $tasks[$index]=$objetoenArray;
        }

        return $this->render('NWPrincipalBundle:Novios:nuestro-checklist.html.twig', array(
            'formAgregar' => $formAgregar->createView(),
            'novia' => $novia->getNombre(),
            'novio' => $novio->getNombre(),
            'tasks'=>$tasks,
            'hayFechaBoda' => $BodaVieja->hayFechaBoda(),
            'contadorFechaBoda' => $BodaVieja->contadorFechaBoda(),
            'fechaBodaFormat' => $BodaVieja->fechaBodaFormat(),
            'saldo' => $user->getSaldoFormat(),
        ));
    }

    public function TaskDeleteAction($id) // Controlador que borra una tarea según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('NWPrincipalBundle:Checklist')->find($id);
        $em->remove($task);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_novios_nuestro-checklist'));
    }

    public function TaskCompletarAction($id) // Controlador que completa una tarea según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('NWPrincipalBundle:Checklist')->find($id);
        $task->setStatus(true);
        $em->persist($task);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_novios_nuestro-checklist'));
    }

    public function TaskDescompletarAction($id) // Controlador que descompleta una tarea según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('NWPrincipalBundle:Checklist')->find($id);
        $task->setStatus(false);
        $em->persist($task);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_novios_nuestro-checklist'));
    }
	
	public function nuestraMesaDeRegalosAction(Request $request)
    {
        // Manejador de Doctrine
        $em = $this->getDoctrine()->getManager();

        $user=$this->getUser();
        $novia=$user->getNovias();
        $novio=$user->getNovios();
        $BodaVieja = $em->getRepository('NWPrincipalBundle:Bodas')->findOneByUsuarioId($user->getId());

        // Nuevo objeto regalo para el formulario
        $formRegaloData = new MesaRegalos();
        $formRegalo = $this->createForm(new RegaloType(), $formRegaloData);
        
        // Recuperando formularios
        if('POST' === $request->getMethod()) {
        
            // Formulario 1
            if ($request->request->has($formRegalo->getName()))
            {
                $formRegalo->handleRequest($request);

                if($formRegalo->isValid())
                {
                    // Obtener datos del formulario
                    $newRegalo = $formRegalo->getData();

                    // Se recupera la categoria original
                    $categoria = $em->getRepository('NWPrincipalBundle:CatRegalos')->find($formRegalo['categoria']->getData());

                    // Asignar valores inexistentes en la nueva clase regalo: usuario, partes pagadas y categoría
                    $newRegalo->setUser($user);
                    $newRegalo->setHorcruxesPagados(0);
                    $newRegalo->setCatregalos($categoria);

                    $em->persist($newRegalo);
                    $em->flush();

                    // Limpiar formulario de nuevo regalo
                    $formRegaloData = new MesaRegalos();
                    $formRegalo = $this->createForm(new RegaloType(), $formRegaloData);
                }
            }
        }

        // Obteniendo la lista de articulos de la mesa de regalos
        $regalos = $em->getRepository('NWPrincipalBundle:MesaRegalos')->findBy(array('usuarioId' => $user->getId()));

        // Convirtiendo los resultados en arrays
        foreach($regalos as $index=>$value)
        {
            $objetoenArray=$regalos[$index]->getValues();
            $regalos[$index]=$objetoenArray;
            $regalos[$index]['categoria']=$em->getRepository('NWPrincipalBundle:CatRegalos')->find($regalos[$index]['categoria'])->getCategoriaName();
        }

        return $this->render('NWPrincipalBundle:Novios:nuestra-mesa-de-regalos.html.twig', array(
            'novia' => $novia->getNombre(),
            'novio' => $novio->getNombre(),
            'regalos' => $regalos,
            'usuarioId' => $user->getId(),
            'formRegalo' => $formRegalo->createView(),
            'hayFechaBoda' => $BodaVieja->hayFechaBoda(),
            'contadorFechaBoda' => $BodaVieja->contadorFechaBoda(),
            'fechaBodaFormat' => $BodaVieja->fechaBodaFormat(),
            'saldo' => $user->getSaldoFormat(),
        ));
    }

    public function RegaloDeleteAction($id) // Controlador que borra un regalo según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $regalo = $em->getRepository('NWPrincipalBundle:MesaRegalos')->find($id);
        $em->remove($regalo);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_novios_nuestra-mesa-de-regalos'));
    }
	
	public function nuestraListaDeInvitadosAction(Request $request)
    {
        // Manejador de Doctrine
        $em = $this->getDoctrine()->getManager();

        $user=$this->getUser();
        $novia=$user->getNovias();
        $novio=$user->getNovios();
        $BodaVieja = $em->getRepository('NWPrincipalBundle:Bodas')->findOneByUsuarioId($user->getId());

        $formAgregarData = new ListaInvitados();
        $formAgregar = $this->createForm(new ListaInvitadosType(), $formAgregarData);

        // Recuperando formularios
        if('POST' === $request->getMethod()) {
        
            // Formulario 1
            if($request->request->has($formAgregar->getName())) {
                // Manejo de los datos del formulario
                $formAgregar->handleRequest($request);

                if($formAgregar->isValid())
                {
                    $newInvitado=$formAgregar->getData();
                    $newInvitado->setUser($user);
                    $newInvitado->setStatus(false);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($newInvitado);
                    $em->flush();

                    // Limpiar formulario para nuvos invitados
                    $formAgregarData = new ListaInvitados();
                    $formAgregar = $this->createForm(new ListaInvitadosType(), $formAgregarData);
                }
            }
        }

        // Obteniendo la lista de invitados en un arreglo de objetos
        $em = $this->getDoctrine()->getManager();
        $invitados = $em->getRepository('NWPrincipalBundle:ListaInvitados')->findBy(array('usuarioId' => $user->getId()));

        // Convirtiendo los resultados en arrays
        $confirmadosInvitados=0;
        foreach($invitados as $index=>$value)
        {
            $objetoenArray=$invitados[$index]->getValues();
            $invitados[$index]=$objetoenArray;

            if($invitados[$index]['status'])
            {
                $confirmadosInvitados++;
            }
        }

        return $this->render('NWPrincipalBundle:Novios:nuestra-lista-de-invitados.html.twig', array(
            'formAgregar' => $formAgregar->createView(),
            'novia' => $novia->getNombre(),
            'novio' => $novio->getNombre(),
            'usuarioId' => $user->getId(),
            'totalInvitados' => count($invitados),
            'confirmadosInvitados' => $confirmadosInvitados,
            'invitados'=>$invitados,
            'hayFechaBoda' => $BodaVieja->hayFechaBoda(),
            'contadorFechaBoda' => $BodaVieja->contadorFechaBoda(),
            'fechaBodaFormat' => $BodaVieja->fechaBodaFormat(),
            'saldo' => $user->getSaldoFormat(),
        ));
    }

    public function InvitadoDeleteAction($id) // Controlador que borra un invitado según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $invitado = $em->getRepository('NWPrincipalBundle:ListaInvitados')->find($id);
        $em->remove($invitado);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_novios_nuestra-lista-de-invitados'));
    }

    public function InvitadoConfirmarAction($id) // Controlador que confirma un invitado según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $invitado = $em->getRepository('NWPrincipalBundle:ListaInvitados')->find($id);
        $invitado->setStatus(true);
        $em->persist($invitado);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_novios_nuestra-lista-de-invitados'));
    }

    public function InvitadoDesconfirmarAction($id) // Controlador que confirma un invitado según el id pasado
    {
        $em = $this->getDoctrine()->getManager();
        $invitado = $em->getRepository('NWPrincipalBundle:ListaInvitados')->find($id);
        $invitado->setStatus(false);
        $em->persist($invitado);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_novios_nuestra-lista-de-invitados'));
    }
	
	public function nuestraCuentaAction(Request $request)
    {
        $lastEmail = $this->getUser()->getNovios()->getEMail();
        $lastEmailGirl = $this->getUser()->getNovias()->getEMail();
        // Manejador de Doctrine
        $em = $this->getDoctrine()->getManager();

        $user=$this->getUser();
        $novia=$user->getNovias();
        $novio=$user->getNovios();
        $BodaVieja = $em->getRepository('NWPrincipalBundle:Bodas')->findOneByUsuarioId($user->getId());

        // Formulario de edición de datos de los novios
        $formData2['novias'] = $novia; // Se recuperan datos de la novia
        $formData2['novios'] = $novio; // Se recuperan datos del novio
        $formNovios = $this->createForm(new EdicionNoviosType(), $formData2); // Formulario de usuarios mezclado con el de novias y novios

        // Formulario de cambio de contraseña
        $form=$this->createFormBuilder()
            ->add('oldPass', 'password', array('required' => true, 'constraints' => new NotBlank()))
            ->add('newPass', 'password', array('required' => true, 'constraints' => array(new NotBlank(), new Length(array('min' => 8)))))
            ->add('Cambiar', 'submit')
            ->getForm();

        // No se ha actualizado la contraseña
        $statusForm = false;
        $tamanoContrasena = false; // El tamaño de la contraseña está bien

        // Formulario de solicitud de retiro estableciendo el maximo que puede retirar
        $nuevaSolicitudRetiro = new SolicitudRetiro();
        $nuevaSolicitudRetiro->setMaximoRetiro($user->getSaldo());
        $formSolicitudRetiro = $this->createForm(new SolicitudRetiroType(), $nuevaSolicitudRetiro);

        // Recuperando formularios
        if('POST' === $request->getMethod()) {
 
            // ¿El formulario que se envió es el de cambio de contraseña?
            if ($request->request->has($form->getName())) {
                // Recuperando datos del formulario de cambio de contraseña
                $form->handleRequest($request);

                if($form->isValid())
                {
                    // Codificando la contraseña escrita para después compararla con la original
                    $encoder_service = $this->get('security.encoder_factory');
                    $encoder = $encoder_service->getEncoder($user);
                    $encoder_pass = $encoder->encodePassword($form["oldPass"]->getData(), $user->getSalt());

                    // Verificar que la contraseña escrita sea correcta
                    if($encoder_pass === $user->getPassword())
                    {
                        // Cambiar contraseña del usuario
                        $user->setPlainPassword($form["newPass"]->getData());
                        $this->get('fos_user.user_manager')->updateUser($user,false);
                        $this->getDoctrine()->getManager()->flush();

                        // Enviar correo a la novia y al novio de que se cambió la contraseña
                        // Novio
                        $message = \Swift_Message::newInstance()
                        ->setSubject("Cambio de contraseña en NewlyWishes.com")
                        ->setFrom("info@newlywishes.com")
                        ->setTo($novio->getEMail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'NWPrincipalBundle:Novios:cambioContrasena.html.twig', array(
                                    'usuario' => $user,
                                    'contrasena' => $form["newPass"]->getData(),
                                )
                            )
                        );
                        $this->get('mailer')->send($message);

                        // Novia
                        $message = \Swift_Message::newInstance()
                        ->setSubject("Cambio de contraseña en NewlyWishes.com")
                        ->setFrom("info@newlywishes.com")
                        ->setTo($novia->getEMail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'NWPrincipalBundle:Novios:cambioContrasena.html.twig', array(
                                    'usuario' => $user,
                                    'contrasena' => $form["newPass"]->getData(),
                                )
                            )
                        );
                        $this->get('mailer')->send($message);
                        
                        // Se manda un mensaje de travesura realizada
                        $this->get('session')->getFlashBag()->set(
                            'notice',
                            'Se cambió la contraseña con éxito. Ya puede utilizar su nueva contraseña para iniciar sesión.'
                        );
                    }
                    else{
                        // Se manda un mensaje de travesura realizada
                        $this->get('session')->getFlashBag()->set(
                            'notice',
                            'No se cambió la contraseña, la contraseña anterior no es la que escribió.'
                        );
                    }
                }
                else
                {
                    // Se manda un mensaje de travesura realizada
                    $this->get('session')->getFlashBag()->set(
                        'notice',
                        'No se cambió la contraseña, hay error en los campos del formulario.'
                    );
                }
            }
            // ¿El formulario que se envió es el de edición de los datos de los novios?
            else if ($request->request->has($formNovios->getName())) {
                // handle the second form
                $formNovios->handleRequest($request);
         
                if ($formNovios->isValid()) {

                    // Recuperando datos de los novios
                    $noviasNew = $formNovios["novias"]->getData();
                    $noviosNew = $formNovios["novios"]->getData();

                    // Checar que los correos no existan ya en la base de datos
                    $userManager = $this->get('fos_user.user_manager');

                    // El correo del novio no tiene que estar ocupado por un usuario
                    $userMail[0] = $userManager->findUserBy(array('email' => $formNovios["novios"]["eMail"]->getData()));
                    // El correo del novio no tiene que estar ocupado por ninguna novia
                    $userMail[1] = is_object($this->getDoctrine()->getRepository('NWUserBundle:Novias')->findOneBy(array('eMail' => $formNovios["novios"]["eMail"]->getData())));
                    // El correo del novio no tiene que estar ocupado por otro novio a menos que ya sea del mismo novio
                    $userMail[2] = is_object($this->getDoctrine()->getRepository("NWUserBundle:Novios")->findOneBy(array('eMail' => $formNovios["novios"]["eMail"]->getData())));
                    if(($userMail[2] && $formNovios["novios"]["eMail"]->getData() == $lastEmail) || !$userMail[2])
                    {$userMail[2] = false;}
                    else{$userMail[2] = true;}
                    // El correo de la novia no tiene que estar ocupado por ningun novio
                    $userMail[3] = is_object($this->getDoctrine()->getRepository("NWUserBundle:Novios")->findOneBy(array('eMail' => $formNovios["novias"]["eMail"]->getData())));
                    // El correo de la novia no tiene que estar ocupado por ninguna novia a menos que ya sea de la misma novia
                    $userMail[4] = is_object($this->getDoctrine()->getRepository("NWUserBundle:Novias")->findOneBy(array('eMail' => $formNovios["novias"]["eMail"]->getData())));
                    if(($userMail[4] && $formNovios["novias"]["eMail"]->getData() == $lastEmailGirl) || !$userMail[4])
                    {$userMail[4] = false;}
                    else{$userMail[4] = true;}
                    // El correo de la novia no tiene que estar ocupado por un usuario a menos que ya sea del mismo usuario
                    $userMail[5] = $userManager->findUserBy(array('email' => $formNovios["novias"]["eMail"]->getData()));
                    if($userMail[5] == $user->getUsername()) {$userMail[5] = false;}

                    if($userMail[0] || $userMail[1] || $userMail[2] || $userMail[3] || $userMail[4] || $userMail[5])
                    {
                        $this->get('session')->getFlashBag()->add('notice', 'Alguno de los correos ya está ocupado');
                        return $this->redirect($this->generateUrl("nw_principal_novios_nuestra-cuenta"));
                    }
                    else{

                        // Agregando datos de la novia
                        $estadoNoviaNew=$this->getDoctrine()->getRepository('NWPrincipalBundle:Estados')->find($formNovios["novias"]["estado"]->getData());
                        
                        $novia->setNombre($formNovios["novias"]["nombre"]->getData());
                        $novia->setsNombre($formNovios["novias"]["sNombre"]->getData());
                        $novia->setAPaterno($formNovios["novias"]["aPaterno"]->getData());
                        $novia->setAMaterno($formNovios["novias"]["aMaterno"]->getData());
                        $novia->setEMail($formNovios["novias"]["eMail"]->getData());
                        $novia->setLada($formNovios["novias"]["lada"]->getData());
                        $novia->setTelefono($formNovios["novias"]["telefono"]->getData());
                        $novia->setCelular($formNovios["novias"]["celular"]->getData());
                        $novia->setDireccion($formNovios["novias"]["direccion"]->getData());
                        $novia->setEstados($estadoNoviaNew);
                        $novia->setCiudad($formNovios["novias"]["ciudad"]->getData());
                        $novia->setCp($formNovios["novias"]["cp"]->getData());

                        // Agregando datos del novio
                        $estadoNovioNew =$this->getDoctrine()->getRepository('NWPrincipalBundle:Estados')->find($formNovios["novios"]["estado"]->getData());

                        $novio->setNombre($formNovios["novios"]["nombre"]->getData());
                        $novio->setsNombre($formNovios["novios"]["sNombre"]->getData());
                        $novio->setAPaterno($formNovios["novios"]["aPaterno"]->getData());
                        $novio->setAMaterno($formNovios["novios"]["aMaterno"]->getData());
                        $novio->setEMail($formNovios["novios"]["eMail"]->getData());
                        $novio->setLada($formNovios["novios"]["lada"]->getData());
                        $novio->setTelefono($formNovios["novios"]["telefono"]->getData());
                        $novio->setCelular($formNovios["novios"]["celular"]->getData());
                        $novio->setDireccion($formNovios["novios"]["direccion"]->getData());
                        $novio->setEstados($estadoNovioNew);
                        $novio->setCiudad($formNovios["novios"]["ciudad"]->getData());
                        $novio->setCp($formNovios["novios"]["cp"]->getData());

                        // Cambio en el usuario novioa
                        $user->setEmail($formNovios["novias"]["eMail"]->getData());

                        // Persistiendo los datos en la base de datos
                        $em->persist($novia);
                        $em->persist($novio);
                        $em->persist($user);
                        $em->flush();

                        // Se mandan correos de los cambios hechos en los datos de la cuenta
                        // Novio
                        $message = \Swift_Message::newInstance()
                        ->setSubject("Se han cambiado los datos de tu cuenta en NewlyWishes.com")
                        ->setFrom("info@newlywishes.com")
                        ->setTo($novio->getEMail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'NWPrincipalBundle:Novios:cambioDatosCuentaNovios.html.twig', array(
                                    
                                )
                            )
                        );
                        $this->get('mailer')->send($message);
                        
                        // Novia
                        $message = \Swift_Message::newInstance()
                        ->setSubject("Se han cambiado los datos de tu cuenta en NewlyWishes.com")
                        ->setFrom("info@newlywishes.com")
                        ->setTo($novia->getEMail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'NWPrincipalBundle:Novios:cambioDatosCuentaNovios.html.twig', array(
                                    
                                )
                            )
                        );
                        $this->get('mailer')->send($message);
                    }
                }
                else{
                    $this->get('session')->getFlashBag()->add('notice', 'Tienes que aceptar los términos y condiciones para poder editar los datos de tu cuenta.');
                    return $this->redirect($this->generateUrl("nw_principal_novios_nuestra-cuenta"));
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

                        // Enviar correo a la novia y al novio de que se cambió la contraseña
                        // Novio
                        $message = \Swift_Message::newInstance()
                        ->setSubject("Solicitud de Retiro en NewlyWishes.com")
                        ->setFrom("info@newlywishes.com")
                        ->setTo($novio->getEMail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'NWPrincipalBundle:Novios:solicitudRetiro.html.twig', array(
                                    'vacio' => "vacio",
                                )
                            )
                        );
                        $this->get('mailer')->send($message);

                        // Novia
                        $message = \Swift_Message::newInstance()
                        ->setSubject("Solicitud de retiro en NewlyWishes.com")
                        ->setFrom("info@newlywishes.com")
                        ->setTo($novia->getEMail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'NWPrincipalBundle:Novios:solicitudRetiro.html.twig', array(
                                    'vacio' => "vacio",
                                )
                            )
                        );
                        $this->get('mailer')->send($message);

                        // Newlywishes finanzas o admin
                        $message = \Swift_Message::newInstance()
                        ->setSubject("Solicitud de Retiro en NewlyWishes.com")
                        ->setFrom("info@newlywishes.com")
                        ->setTo("admin@newlywishes.com")
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'NWPrincipalBundle:Novios:solicitudRetiro.html.twig', array(
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

         // Datos de la novia para plantilla
        $noviaInfo['nombreCompleto']=$novia->getNombre().' '.$novia->getSNombre().' '.$novia->getAPaterno().' '.$novia->getAMaterno();
        $noviaInfo['email']=$novia->getEMail();
        $noviaInfo['lada']=$novia->getLada();
        $noviaInfo['telefono']=$novia->getTelefono();
        $noviaInfo['celular']=$novia->getCelular();
        $noviaInfo['direccion']=$novia->getDireccion();
        $noviaInfo['estado']=$novia->getEstados()->getEstado();
        $noviaInfo['ciudad']=$novia->getCiudad();
        $noviaInfo['cp']=$novia->getCp();

        // Datos del novio para plantilla
        $novioInfo['nombreCompleto']=$novio->getNombre().' '.$novio->getSNombre().' '.$novio->getAPaterno().' '.$novio->getAMaterno();
        $novioInfo['email']=$novio->getEMail();
        $novioInfo['lada']=$novio->getLada();
        $novioInfo['telefono']=$novio->getTelefono();
        $novioInfo['celular']=$novio->getCelular();
        $novioInfo['direccion']=$novio->getDireccion();
        $novioInfo['estado']=$novio->getEstados()->getEstado();
        $novioInfo['ciudad']=$novio->getCiudad();
        $novioInfo['cp']=$novio->getCp();


        // Renderear la plantilla con la información necesaria
        return $this->render('NWPrincipalBundle:Novios:nuestra-cuenta.html.twig', array(
            'form' => $form->createView(),
            'formNovios' => $formNovios->createView(),
            'formSolicitudRetiro' => $formSolicitudRetiro->createView(),
            'novia' => $novia->getNombre(),
            'novio' => $novio->getNombre(),
            'noviaInfo' => $noviaInfo,
            'novioInfo' => $novioInfo,
            'statusForm' => $statusForm,
            'tamanoContrasena' => $tamanoContrasena,
            'hayFechaBoda' => $BodaVieja->hayFechaBoda(),
            'contadorFechaBoda' => $BodaVieja->contadorFechaBoda(),
            'fechaBodaFormat' => $BodaVieja->fechaBodaFormat(),
            'saldo' => $user->getSaldoFormat(),
        ));
    }
	
	public function nuestraInformacionBancariaAction(Request $request)
    {
        // Manejador de Doctrine
        $em = $this->getDoctrine()->getManager();

        $user=$this->getUser();
        $novia=$user->getNovias();
        $novio=$user->getNovios();
        $BodaVieja = $em->getRepository('NWPrincipalBundle:Bodas')->findOneByUsuarioId($user->getId());

        return $this->render('NWPrincipalBundle:Novios:nuestra-informacion-bancaria.html.twig', array(
            'novia' => $novia->getNombre(),
            'novio' => $novio->getNombre(),
            'hayFechaBoda' => $BodaVieja->hayFechaBoda(),
            'contadorFechaBoda' => $BodaVieja->contadorFechaBoda(),
            'fechaBodaFormat' => $BodaVieja->fechaBodaFormat(),
        ));
    }
	
	public function nuestrasComprasAction(Request $request)
    {
        // Manejador de Doctrine
        $em = $this->getDoctrine()->getManager();

        $user=$this->getUser();
        $novia=$user->getNovias();
        $novio=$user->getNovios();
        $BodaVieja = $em->getRepository('NWPrincipalBundle:Bodas')->findOneByUsuarioId($user->getId());

        $formDiaBoda = $this->createForm(new DiaBodaType());

        // Formulario de buscador de artículos
        $formBuscarArticulo = $this->createForm(new BusquedaArticulosType());

        // Servicio de búsqueda y carga de artículos de la base de datos
        $buscador = $this->get('articulos_buscador');

        $resultados = false;

        // Recuperando formularios
        if('POST' === $request->getMethod()) {

            // Formulario  de búqueda de artículos
            if ($request->request->has($formBuscarArticulo->getName())) {
                $formBuscarArticulo->handleRequest($request);
                if ($formBuscarArticulo->isValid()) {

                    // Servicio de búsqueda y carga de artículos y proveedores de la base de datos
                    $buscador = $this->get('articulos_buscador');
                    $resultados = $buscador->busquedaResultados($formBuscarArticulo->getData());
                }
            }
        }

        return $this->render('NWPrincipalBundle:Novios:nuestras-compras.html.twig', array(
            'novia' => $novia->getNombre(),
            'novio' => $novio->getNombre(),
            'hayFechaBoda' => $BodaVieja->hayFechaBoda(),
            'contadorFechaBoda' => $BodaVieja->contadorFechaBoda(),
            'formBuscarArticulo' => $formBuscarArticulo->createView(),
            'resultados' => $resultados, // Resultados de la búsqueda
            'fechaBodaFormat' => $BodaVieja->fechaBodaFormat(),
            'saldo' => $user->getSaldoFormat(),
        ));
    }

}