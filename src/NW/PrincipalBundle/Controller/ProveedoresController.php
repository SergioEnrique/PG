<?php

namespace NW\PrincipalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use NW\PrincipalBundle\Form\Type\EdicionProveedorType;
use NW\PrincipalBundle\Form\Type\ArticuloType;
use NW\PrincipalBundle\Form\Type\ArticuloEditType;
use NW\PrincipalBundle\Form\Type\AnuncioType;
use NW\PrincipalBundle\Form\Type\BannersType;
use NW\PrincipalBundle\Form\Type\SeleccionPlanType;
use NW\PrincipalBundle\Form\Type\ProveedorPublicoType;
use NW\PrincipalBundle\Form\Type\NuevoCodigoVentaType;

use NW\PrincipalBundle\Entity\Articulos;
use NW\PrincipalBundle\Entity\FotosArticulos;
use NW\PrincipalBundle\Entity\Banners;
use NW\PrincipalBundle\Entity\Anuncios;
use NW\PrincipalBundle\Entity\GaleriaProveedor;
use NW\PrincipalBundle\Entity\Codigos;
use NW\UserBundle\Entity\registroproveedores;


class ProveedoresController extends Controller
{
    public function micuentaAction(Request $request)
    {
        // Manejador de entidades
        $em = $this->getDoctrine()->getEntityManager();

        // Obtener usuario y proveedor
        $user = $this->getUser();
        $proveedorObject = $user->getRegistroproveedores();

        // Formulario de edición de datos del proveedor
        $formProveedorData["tipoPersona"] = $proveedorObject->getTipoPersona();
        $formProveedorData["nombreRazon"] = $proveedorObject->getNombreRazon();
        $formProveedorData["apellidoPaterno"] = $proveedorObject->getApellidoPaterno();
        $formProveedorData["apellidoMaterno"] = $proveedorObject->getApellidoMaterno();
        $formProveedorData["rfc"] = $proveedorObject->getRfc();
        $formProveedorData["email"] = $proveedorObject->getEmail();
        $formProveedorData["lada"] = $proveedorObject->getLada();
        $formProveedorData["telefono"] = $proveedorObject->getTelefono();
        $formProveedorData["celular"] = $proveedorObject->getCelular();
        $formProveedorData["direccion"] = $proveedorObject->getDireccion();
        $formProveedorData["ciudad"] = $proveedorObject->getCiudad();
        $formProveedorData["cp"] = $proveedorObject->getCp();
        $formProveedorData["estado"] = $proveedorObject->getEstados()->getId();
        $formProveedor = $this->createForm(new EdicionProveedorType(), $formProveedorData);

        // Formulario de cambio de plan
        $formProveedorPlanData["plan"] = $proveedorObject->getPlan();
        $formProveedorPlan = $this->createForm(new SeleccionPlanType(), $formProveedorPlanData);

        // Formulario de cambio de contraseña
        $form = $this->createFormBuilder()
            ->add('oldPass', 'password')
            ->add('newPass', 'password')
            ->add('Cambiar', 'submit')
            ->getForm();

        // Formulario de Imagen Pública de la empresa
        $formProveedorPublicoData["nombreComercial"] = $proveedorObject->getNombreComercial();
        $formProveedorPublicoData["descripcion"] = $proveedorObject->getDescripcion();
        $formProveedorPublico = $this->createForm(new ProveedorPublicoType(), $formProveedorPublicoData);

        // No se ha actualizado la contraseña
        $statusForm=false;

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
                        $this->getDoctrine()->getEntityManager()->flush();
                        
                        // Ya se actualizó la contraseña
                        $statusForm=true;
                    }            
                }
            }
            // ¿El formulario que se envió es el de edición de los datos del proveedor?
            else if ($request->request->has($formProveedor->getName())) {

                // handle the second form
                $formProveedor->handleRequest($request);
         
                if ($formProveedor->isValid()) {

                    // Recuperando datos del proveedor
                    $ProveedorNew = $formProveedor->getData();

                    // Agregando datos del proveedor
                    $estadoProveedorNew=$this->getDoctrine()->getRepository('NWPrincipalBundle:Estados')->find($formProveedor["estado"]->getData());
                    
                    $proveedorObject->setTipoPersona($formProveedor["tipoPersona"]->getData());
                    $proveedorObject->setNombreRazon($formProveedor["nombreRazon"]->getData());
                    $proveedorObject->setApellidoPaterno($formProveedor["apellidoPaterno"]->getData());
                    $proveedorObject->setApellidoMaterno($formProveedor["apellidoMaterno"]->getData());
                    $proveedorObject->setRfc($formProveedor["rfc"]->getData());
                    $proveedorObject->setEMail($formProveedor["email"]->getData());
                    $proveedorObject->setLada($formProveedor["lada"]->getData());
                    $proveedorObject->setTelefono($formProveedor["telefono"]->getData());
                    $proveedorObject->setCelular($formProveedor["celular"]->getData());
                    $proveedorObject->setDireccion($formProveedor["direccion"]->getData());
                    $proveedorObject->setEstados($estadoProveedorNew);
                    $proveedorObject->setCiudad($formProveedor["ciudad"]->getData());
                    $proveedorObject->setCp($formProveedor["cp"]->getData());

                    // Persistiendo los datos en la base de datos
                    $em->persist($proveedorObject);
                    $em->flush();
                }http://www.taringa.net/posts/linux/16009015/Xubuntu-Ubuntu-12-04-con-efectos-compiz.html
            }
            // ¿El formulario que se envió es el de edición de plan?
            else if ($request->request->has($formProveedorPlan->getName())) {

                // handle the third form
                $formProveedorPlan->handleRequest($request);
         
                if ($formProveedorPlan->isValid()) {

                    // Cargando el plan antiguo
                    $planActual = $proveedorObject->getPlan();

                    // Agregando plan al proveedor actual
                    $proveedorObject->setPlan($formProveedorPlan["plan"]->getData());

                    // Borrar rol antiguo del proveedor
                    switch ($planActual) {
                        case 'anuncioEspecial':
                            $user->removeRole('ROLE_PROVEEDOR_ANUNCIO');
                            break;
                        case 'anuncioPlus':
                            $user->removeRole('ROLE_PROVEEDOR_ANUNCIO');
                            break;
                        case 'basico':
                            $user->removeRole('ROLE_PROVEEDOR_BASICO');
                            break;
                        case 'estandar':
                            $user->removeRole('ROLE_PROVEEDOR_ESTANDAR');
                            break;
                        case 'plus':
                            $user->removeRole('ROLE_PROVEEDOR_PLUS');
                            break;
                        default:
                            $user->removeRole('ROLE_PROVEEDOR');
                            break;
                    }

                    // Agregando rol de plan de proveedor
                    switch ($formProveedorPlan["plan"]->getData()) {
                        case 'anuncioEspecial':
                            $user->addRole('ROLE_PROVEEDOR_ANUNCIO');
                            break;
                        case 'anuncioPlus':
                            $user->addRole('ROLE_PROVEEDOR_ANUNCIO');
                            break;
                        case 'basico':
                            $user->addRole('ROLE_PROVEEDOR_BASICO');
                            break;
                        case 'estandar':
                            $user->addRole('ROLE_PROVEEDOR_ESTANDAR');
                            break;
                        case 'plus':
                            $user->addRole('ROLE_PROVEEDOR_PLUS');
                            break;
                        default:
                            $user->addRole('ROLE_PROVEEDOR');
                            break;
                    }

                    // Persistiendo los datos en la base de datos
                    $em->persist($proveedorObject);
                    $em->flush();

                    // Saliendo de la sesión
                    $this->get('security.context')->setToken(null);
                    $this->get('request')->getSession()->invalidate();
                    $redirect = $this->generateUrl('nw_user_registroproveedores');
                    return $this->redirect($redirect);
                }
            }
            // ¿El formulario que se envió es el de edición de imagen comercial?
            else if ($request->request->has($formProveedorPublico->getName())) {

                // handle the fourth form
                $formProveedorPublico->handleRequest($request);
         
                if ($formProveedorPublico->isValid()) {

                    // Se actualiza el logo por el nuevo si es que se definió uno y se sube
                    if($formProveedorPublico["file"]->getData())
                    {
                        $proveedorObject->setFile($formProveedorPublico["file"]->getData());
                        $proveedorObject->upload();
                    }

                    // Se sube una nueva imagen a la galería del producto si es que se definió una
                    if($formProveedorPublico["fileGaleria"]->getData())
                    {
                        $nuevaFotoGaleria = new GaleriaProveedor();
                        $nuevaFotoGaleria->setProveedor($proveedorObject);
                        $nuevaFotoGaleria->setFile($formProveedorPublico["fileGaleria"]->getData());
                        $nuevaFotoGaleria->upload(false); // False o la id del usuario es lo mismo

                        $em->persist($nuevaFotoGaleria);
                    }

                    $proveedorObject->setNombreComercial($formProveedorPublico["nombreComercial"]->getData());
                    $proveedorObject->setDescripcion($formProveedorPublico["descripcion"]->getData());

                    $em->persist($proveedorObject);
                    $em->flush();
                }
            }
        }

         // Datos del proveedor
        $proveedor['tipoPersona']=$proveedorObject->getTipoPersona();
        $proveedor['nombre']=$proveedorObject->getNombreRazon();

        if ($proveedor['tipoPersona']==='fisica')
        {
             $proveedor['nombre']=$proveedorObject->getNombreRazon().' '.$proveedorObject->getApellidoPaterno().' '.$proveedorObject->getApellidoMaterno();
        }

        $proveedor['cuenta']=$proveedorObject->getId();
        $proveedor['tipoPersona']=$proveedorObject->getTipoPersona();
        $proveedor['rfc']=$proveedorObject->getRfc();
        $proveedor['email']=$proveedorObject->getEmail();
        $proveedor['lada']=$proveedorObject->getLada();
        $proveedor['telefono']=$proveedorObject->getTelefono();
        $proveedor['celular']=$proveedorObject->getCelular();
        $proveedor['direccion']=$proveedorObject->getDireccion();
        $proveedor['estado']=$proveedorObject->getEstados()->getEstado();
        $proveedor['ciudad']=$proveedorObject->getCiudad();
        $proveedor['cp']=$proveedorObject->getCp();
        $proveedor['planName']=$proveedorObject->getPlanName();
        $proveedor['nombreComercial']=$proveedorObject->getNombreComercial();
        $proveedor['descripcion']=$proveedorObject->getDescripcion();
        $proveedor['logo']=$proveedorObject->getWebPath();
        $proveedor['galeria']=$proveedorObject->getGaleriaArray();
        // Fin de datos del proveedor

        return $this->render('NWPrincipalBundle:Proveedores:micuenta.html.twig', array(
            'form' => $form->createView(),
            'formProveedor' => $formProveedor->createView(),
            'formProveedorPlan' => $formProveedorPlan->createView(),
            'formProveedorPublico' => $formProveedorPublico->createView(),
            'proveedor' => $proveedor,
            'statusForm' => $statusForm
        ));
    }

    public function misproductosAction(Request $request)
    {
        if(false === $this->get('security.context')->isGranted('ROLE_PROVEEDOR_BASICO')) {
            throw new AccessDeniedException();
        }
    	// Manejador de Doctrine
        $em = $this->getDoctrine()->getManager();
		
		// Obtener Datos del proveedor
        $user=$this->getUser();
        $proveedorObject=$user->getRegistroproveedores();
        $proveedor['nombre']=$proveedorObject->getNombreRazon();
        $proveedor['cuenta']=$proveedorObject->getId();

        // Formulario de nuevo artículo
        $formArticuloData['datos'] = new Articulos();
        $formArticuloData['foto'] = new FotosArticulos();
        $formArticulo = $this->createForm(new ArticuloType(), $formArticuloData);

        // Formulario de edición de artículo
        $formArticuloEditData['datos'] = new Articulos();
        $formArticuloEditData['foto'] = new FotosArticulos();
        $formArticuloEdit = $this->createForm(new ArticuloEditType(), $formArticuloEditData);

        // Recuperando formularios
        if('POST' === $request->getMethod()) {
        
            // Formulario 1 (Artículo nuevo)
            if ($request->request->has($formArticulo->getName())) {
                // handle the first form
                $formArticulo->handleRequest($request);
         
                if ($formArticulo->isValid()) {
        			
                    // Recuperando información de formulario
        			$newArticulo = $formArticulo["datos"]->getData();
        			$newArticuloFoto = $formArticulo["foto"]->getData();

                    // Si se eligió categoriá para el artículo, ésta se ubica, si no, entonces se mete en la categoría "Otros(id=27)"
                    if($formArticulo["categoria"]["categorias"]->getData())
                    {
                        $newArticuloCategoria = $this->getDoctrine()->getRepository('NWPrincipalBundle:Categorias')->find($formArticulo["categoria"]["categorias"]->getData());
                    }
                    else
                    {
                        $newArticuloCategoria = $this->getDoctrine()->getRepository('NWPrincipalBundle:Categorias')->find(27);
                    }
                    
                	$newArticulo->setUser($user);// Setteando el usuario en el artículo
                    $newArticulo->setCategoria($newArticuloCategoria);// Setteando la categoría en el artículo
                	$newArticuloFoto->setArticulo($newArticulo);// Setteando el articulo en la foto
                	$newArticulo->setTamanos(explode(',', $formArticulo["datos"]["tamanos"]->getData()));// Convierte en array los tamaños
                	$newArticuloFoto->upload($user->getId());// Subiendo la imagen

                    $em->persist($newArticulo);
                    $em->persist($newArticuloFoto);
                    $em->flush();
                }
            }
            // Formulario2 (Editar artículo)
            else if ($request->request->has($formArticuloEdit->getName())) {
                // handle the second form
                $formArticuloEdit->handleRequest($request);
         
                if ($formArticuloEdit->isValid()) {

                    // Persistiendo los datos en la base de datos
                    $em = $this->getDoctrine()->getEntityManager();
        
                    // Recuperando información de formulario
                    $newArticulo = $formArticuloEdit["datos"]->getData();

                    // Si se eligió categoriá para el artículo, ésta se ubica, si no, entonces se mete en la categoría "Otros(id=27)"
                    if($formArticuloEdit["categoria"]["categorias"]->getData())
                        $newArticuloCategoria = $this->getDoctrine()->getRepository('NWPrincipalBundle:Categorias')->find($formArticuloEdit["categoria"]["categorias"]->getData());
                    else
                        $newArticuloCategoria = $this->getDoctrine()->getRepository('NWPrincipalBundle:Categorias')->find(27);

                    // Se obtiene el antiguo objeto del artículo
                    $oldArticulo = $this->getDoctrine()->getRepository('NWPrincipalBundle:Articulos')->find($formArticuloEdit["id"]->getData());
                    
                    // Se actualizan los valores del artículo
                    $oldArticulo->setNombre($formArticuloEdit["datos"]["nombre"]->getData());
                    $oldArticulo->setIdInterno($formArticuloEdit["datos"]["idInterno"]->getData());
                    $oldArticulo->setDescripcion($formArticuloEdit["datos"]["descripcion"]->getData());
                    $oldArticulo->setStock($formArticuloEdit["datos"]["stock"]->getData());
                    $oldArticulo->setPrecio($formArticuloEdit["datos"]["precio"]->getData());
                    $oldArticulo->setPrecioPromocion($formArticuloEdit["datos"]["precioPromocion"]->getData());
                    $oldArticulo->setTamanos(explode(',', $formArticuloEdit["datos"]["tamanos"]->getData()));// Convierte en array los tamaños
                    $oldArticulo->setTipo($formArticuloEdit["datos"]["tipo"]->getData());
                    $oldArticulo->setEstatus($formArticuloEdit["datos"]["estatus"]->getData());
                    $oldArticulo->setCategoria($newArticuloCategoria);// Setteando la categoría en el artículo

                    // Si se cargó una nueva foto:
                    if($formArticuloEdit["foto"]["file"]->getData())
                    {
                        $newArticuloFoto = $formArticuloEdit["foto"]->getData(); // Obteniendo objeto foto del formulario
                        $newArticuloFoto->setArticulo($oldArticulo); // Setteando el articulo en la foto antigua
                        $newArticuloFoto->upload($user->getId()); // Subiendo la imagen
                        $em->persist($newArticuloFoto); // Persistiendo a la base de datos
                    }

                    // Se persiste el artículo en la base de datos
                    $em->persist($oldArticulo);
                    $em->flush();
                }
            }
        }

        // Obteniendo la lista de artículos en un arreglo de objetos
        $articulos = $em->getRepository('NWPrincipalBundle:Articulos')->findBy(array('usuarioId' => $user->getId()));

        // Convirtiendo los resultados en arrays
        foreach($articulos as $index=>$value)
        {
            $objetoenArray=$articulos[$index]->getValues();
            $articulos[$index]=$objetoenArray;

            foreach($articulos[$index]['fotos'] as $indice=>$valor)
            {
            	$objeto2enArray=$articulos[$index]['fotos'][$indice]->getValues(false);
            	$articulos[$index]['fotos'][$indice]=$objeto2enArray;
            }
        }

        return $this->render('NWPrincipalBundle:Proveedores:misproductos.html.twig', array(
            'proveedor' => $proveedor,
            'formArticulo' => $formArticulo->createView(),
            'formArticuloEdit' => $formArticuloEdit->createView(),
            'articulos' => $articulos,
        ));
    }

    public function ProductoDeleteAction($id) // Controlador que borra un producto según el id pasado
    {
        // Manejador de entidades Doctrine
        $em = $this->getDoctrine()->getManager();

        // Objeto producto que se eliminará
        $producto = $em->getRepository('NWPrincipalBundle:Articulos')->find($id);

        // Buscar fotos del producto
        $fotosArray = $em->getRepository('NWPrincipalBundle:FotosArticulos')->findBy(array('articuloId' => $id));

        // Borrar fotos del producto
        foreach($fotosArray as $index=>$fotoObj)
        {
            $em->remove($fotoObj);
        }

        // Borrar Producto
        $em->remove($producto);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_proveedores_misproductos'));
    }

    public function misbannersAction(Request $request)
    {
        if(false === $this->get('security.context')->isGranted('ROLE_PROVEEDOR_BASICO')) {
            throw new AccessDeniedException();
        }

        // Entity Manager
    	$em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $proveedorObject = $user->getRegistroproveedores();

        $proveedor['nombre'] = $proveedorObject->getNombreRazon();
        $proveedor['cuenta'] = $proveedorObject->getId();

        // Formulario del anuncio
        $formBannerData = new Banners();
        $formBanner = $this->createForm(new BannersType(), $formBannerData);

        // Recuperando formularios
        if('POST' === $request->getMethod()) {
        
            // Formulario 1 (Agregar Banner)
            if ($request->request->has($formBanner->getName())) {
                // handle the first form
                $formBanner->handleRequest($request);
         
                if ($formBanner->isValid()) {
                    
                    // Se busca una artículo con el idInterno (name) especificado en el formulario
                    $articulosEntity = $em->getRepository('NWPrincipalBundle:Articulos');
                    $articulo = $articulosEntity->findOneBy(array('usuarioId' => $user->getId(), 'idInterno' => $formBanner["name"]->getData()));

                    if($articulo)
                    {  
                        $formBannerData->setUser($user);
                        $formBannerData->setArticulo($articulo);
                        $formBannerData->setFile($formBanner["file"]->getData());
                        $formBannerData->upload($user->getId());

                        $em->persist($formBannerData);
                        $em->flush();
                    }
                    else
                    {
                        return new Response("No existe un artículo con el ID Interno ".$formBanner["name"]->getData());
                    }
				    
                }
            }
            // Formulario2
            /*else if ($request->request->has($formOtro->getName())) {
                // handle the second form
                $formOtro->handleRequest($request);
         
                if ($formOtro->isValid()) {
        
                    //Contenido
                }
            }*/
        }

        // Obteniendo la lista de banners en un arreglo de objetos
        $banners = $em->getRepository('NWPrincipalBundle:Banners')->findBy(array('usuarioId' => $user->getId()));

        // Convirtiendo los resultados en arrays
        foreach($banners as $index=>$value)
        {
            $objetoenArray=$banners[$index]->getValues($user->getId());
            $banners[$index]=$objetoenArray;
        }

        return $this->render('NWPrincipalBundle:Proveedores:misbanners.html.twig', array(
            'proveedor' => $proveedor,
            'formBanner' => $formBanner->createView(),
            'banners' => $banners,
        ));
    }

    public function BannerDeleteAction($id) // Controlador que borra un banner según el id pasado
    {
        // Manejador de entidades Doctrine
        $em = $this->getDoctrine()->getManager();

        // Objeto banner que se eliminará
        $banner = $em->getRepository('NWPrincipalBundle:Banners')->find($id);

        // Borrar Banner
        $em->remove($banner);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_proveedores_misbanners'));
    }

    public function misanunciosAction(Request $request)
    {
        if(false === $this->get('security.context')->isGranted('ROLE_PROVEEDOR_ANUNCIO')) {
            throw new AccessDeniedException();
        }

    	$em = $this->getDoctrine()->getManager();

        $user=$this->getUser();
        $proveedorObject=$user->getRegistroproveedores();

        $proveedor['nombre']=$proveedorObject->getNombreRazon();
        $proveedor['cuenta']=$proveedorObject->getId();

        $nombreComercial = $proveedorObject->getNombreComercial();

        $no_permitidas = array ('á','é','í','ó','ú','Á','É','Í','Ó','Ú','ñ','Ñ');
        $si_permitidas = array ('a','e','i','o','u','A','E','I','O','U','n','N');

        $nombreComercial = str_replace($no_permitidas, $si_permitidas, $nombreComercial);
        
        // Se carga un anuncio del proveedor si es que ya existe
      	$AnuncioViejo = $em->getRepository('NWPrincipalBundle:Anuncios')->findOneByUsuarioId($user->getId());

      	if ($AnuncioViejo)
      	{
      		$anuncioWebpage = $AnuncioViejo->getWebpage();
      		$anuncioImagePath = $AnuncioViejo->getWebPath($user->getId());
      	}
      	else
      	{
      		$anuncioWebpage = "No se ha definido ningun sitio";
      		$anuncioImagePath = "img/anuncio.jpg";
      	}

        // Formulario del anuncio
        $formAnuncioData = new Anuncios();
        $formAnuncio = $this->createForm(new AnuncioType(), $formAnuncioData);

        // Recuperando formularios
        if('POST' === $request->getMethod()) {
        
            // Formulario 1 (Editar Anuncio)
            if ($request->request->has($formAnuncio->getName())) {
                // handle the first form
                $formAnuncio->handleRequest($request);
         
                if ($formAnuncio->isValid()) {
      
                	// Si ya existe un anuncio se actualiza, si no se hace una nuevo
      				if ($AnuncioViejo) {
      					$AnuncioViejo->setFile($formAnuncioData->getFile());
      					$AnuncioViejo->setWebpage($formAnuncioData->getWebpage());
      					$AnuncioViejo->upload($user->getId());

      					$em->persist($AnuncioViejo);
				    }
				    else{
					    $formAnuncioData->setUser($user);
	      				$formAnuncioData->upload($user->getId());

	                    $em->persist($formAnuncioData);
                	}

                    $em->flush();

                    return $this->redirect($this->generateUrl('nw_principal_proveedores_misanuncios'));
                }
            }
            // Formulario2
            /*else if ($request->request->has($formOtro->getName())) {
                // handle the second form
                $formOtro->handleRequest($request);
         
                if ($formOtro->isValid()) {
        
                    //Contenido
                }
            }*/
        }

        return $this->render('NWPrincipalBundle:Proveedores:misanuncios.html.twig', array(
        	'formAnuncio' => $formAnuncio->createView(),
            'proveedor' => $proveedor,
            'anuncioWebpage' => $anuncioWebpage,
            'anuncioImagePath' => $anuncioImagePath,
            'nombreComercial' => $nombreComercial,
        ));
    }

    public function misresenasAction()
    {
        if(false === $this->get('security.context')->isGranted('ROLE_PROVEEDOR_BASICO')) {
            throw new AccessDeniedException();
        }

        $user=$this->getUser();
        $proveedorObject=$user->getRegistroproveedores();

        $proveedor['nombre']=$proveedorObject->getNombreRazon();
        $proveedor['cuenta']=$proveedorObject->getId();

        // Servicio de Reseñas
        $resenasService = $this->get('resenas_service');

        // Obtener toda la información de las reseñas del proveedor
        $resenas = $resenasService->getResenas($proveedorObject->getId());

        return $this->render('NWPrincipalBundle:Proveedores:misresenas.html.twig', array(
            'proveedor' => $proveedor,
            'resenas' => $resenas,
        ));
    }

    public function miestadodecuentaAction()
    {
        if(false === $this->get('security.context')->isGranted('ROLE_PROVEEDOR_BASICO')) {
            throw new AccessDeniedException();
        }

        $user=$this->getUser();
        $proveedorObject=$user->getRegistroproveedores();

        $proveedor['nombre']=$proveedorObject->getNombreRazon();
        $proveedor['cuenta']=$proveedorObject->getId();

        return $this->render('NWPrincipalBundle:Proveedores:miestadodecuenta.html.twig', array(
            'proveedor' => $proveedor,
        ));
    }

    public function miscodigosAction(Request $request)
    {
        if(false === $this->get('security.context')->isGranted('ROLE_PROVEEDOR_BASICO')) {
            throw new AccessDeniedException();
        }

        // Entity Manager
        $em = $this->getDoctrine()->getManager();

        $proveedoresEntity = $em->getRepository('NWUserBundle:registroproveedores');
        $codigosEntity = $em->getRepository('NWPrincipalBundle:Codigos');

        $user = $this->getUser();
        $proveedorObject = $user->getRegistroproveedores();

        $proveedor['nombre'] = $proveedorObject->getNombreRazon();
        $proveedor['cuenta'] = $proveedorObject->getId();

        $codigosVentaEntity = $em->getRepository('NWPrincipalBundle:Codigos');
        $codigoObject = new Codigos();
        $codigoObject->setProveedor($proveedorObject);

        $formNuevoCodigo = $this->createForm(new NuevoCodigoVentaType(), $codigoObject);

        if('POST' === $request->getMethod())
        {
            $formNuevoCodigo->handleRequest($request);
            if ($formNuevoCodigo->isValid())
            {
                $vendedor = $proveedoresEntity->find($codigoObject->getVendedorId());

                if($vendedor)
                {
                    $codigoObject->setVendedor($vendedor);
                    $em->persist($codigoObject);
                    $em->flush();
                }
                else
                {
                    return new Response('No existe el vendedor especificado');
                }
            }
        }

        $resultados = $codigosEntity->findBy(array('proveedorId' => $proveedorObject->getId()));

        foreach($resultados as $index=>$value)
        {
            $resultados[$index] = $value->getValues();
        }

        return $this->render('NWPrincipalBundle:Proveedores:miscodigos.html.twig', array(
            'proveedor' => $proveedor,
            'formNuevoCodigo' => $formNuevoCodigo->createView(),
            'resultados' => $resultados,
        ));
    }

    public function CodigoDeleteAction($codigo)
    {
        // Manejador de entidades Doctrine
        $em = $this->getDoctrine()->getManager();

        // Objeto codigo que se eliminará
        $codigo = $em->getRepository('NWPrincipalBundle:Codigos')->find($codigo);

        // Borrar Codigo
        $em->remove($codigo);
        $em->flush();

        return $this->redirect($this->generateUrl('nw_principal_proveedores_miscodigos'));
    }

    public function miinformacionbancariaAction()
    {
        if(false === $this->get('security.context')->isGranted('ROLE_PROVEEDOR_BASICO')) {
            throw new AccessDeniedException();
        }

        $user=$this->getUser();
        $proveedorObject=$user->getRegistroproveedores();

        $proveedor['nombre']=$proveedorObject->getNombreRazon();
        $proveedor['cuenta']=$proveedorObject->getId();

        return $this->render('NWPrincipalBundle:Proveedores:miinformacionbancaria.html.twig', array(
            'proveedor' => $proveedor,
        ));
    }

    public function tutorialesAction()
    {
        $user=$this->getUser();
        $proveedorObject=$user->getRegistroproveedores();

        $proveedor['nombre']=$proveedorObject->getNombreRazon();
        $proveedor['cuenta']=$proveedorObject->getId();

        return $this->render('NWPrincipalBundle:Proveedores:mtutoriales.html.twig', array(
            'proveedor' => $proveedor,
        ));
    }

}