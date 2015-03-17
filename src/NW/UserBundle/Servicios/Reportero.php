<?php
// src/NW/UserBundle/Servicios/Reportero.php
namespace NW\UserBundle\Servicios;

class Reportero
{
    protected $em; // Doctrine Entity Manager
    protected $userManager; // Manejador de usuarios FOS
    protected $encoderService; // Servicio de codificación para la contraseña

    public function __construct($em, $userManager, $encoderService)
    {
    	$this->em = $em;
        $this->userManager = $userManager;
        $this->encoderService = $encoderService;
    }

	public function registrarReportero($reportero, $userName, $userPass)
    {
        // Obtener estado
        $estadoEntity = $this->em->getRepository('NWPrincipalBundle:Estados');
        $estado = $estadoEntity->find($reportero->getEstadoId());

        // Creando Usuario
        $user = $this->userManager->createUser(); 
        $user->setUsername($userName);
        $user->setEmail($reportero->getEmail());
        $user->setPlainPassword($userPass);
        $user->setEnabled(true);
        $user->addRole('ROLE_REPORTERO');
        $user->setSaldo(0);

        // Agregando Reportero
        $reportero->setUser($user);
        $reportero->setEstado($estado);

        // Persistiendo a base de datos
        $this->em->persist($user);
        $this->em->persist($reportero);
        $this->em->flush();

        return true;
    }

    public function actualizarReportero($reportero)
    {
        // Settear estado
        $estadoEntity = $this->em->getRepository('NWPrincipalBundle:Estados');
        $estado = $estadoEntity->find($reportero->getEstadoId());
        $reportero->setEstado($estado);

        // Persistiendo a base de datos
        $this->em->persist($reportero);
        $this->em->flush();

        return true;
    }

    public function getReportero($user)
    {
        // Entidad de Reportero
        $reporteroEntity = $this->em->getRepository('NWUserBundle:Reportero');

        // Obtener reportero
        $reportero = $reporteroEntity->findOneBy(array('usuarioId' => $user->getId()));

        return $reportero;
    }

    public function getReporteroArray($user)
    {
        // Reportero como arreglo
        return $this->getReportero($user)->getValues();
    }

    public function changePass($user, $oldPass, $newPass)
    {
        // Codificando la contraseña escrita para compararla con la real
        $encoder = $this->encoderService->getEncoder($user);
        $encoderPass = $encoder->encodePassword($oldPass, $user->getSalt());

        // Verificar que la contraseña anterior sea correcta
        if($encoderPass === $user->getPassword())
        {
            // Cambiar contraseña del usuario
            $user->setPlainPassword($newPass);
            $this->userManager->updateUser($user, false);
            $this->em->flush();

            return true;
        }
    }

    public function getCategorias()
    {
        $categoriasEntity = $this->em->getRepository('NWPrincipalBundle:CategoriaReportero');
        $categorias = $categoriasEntity->findAll();

        foreach ($categorias as $key => $value) {
            $categoriasArray[$value->getId()] = $value->getCatName();
        }

        return $categoriasArray;
    }

    public function cargarArticulo($articulo)
    {
        // Asignar categoría
        $categoriasEntity = $this->em->getRepository('NWPrincipalBundle:CategoriaReportero');
        $categoria = $categoriasEntity->find($articulo->getCategoriaId());
        $articulo->setCategoria($categoria);
        $articulo->setEstatus('Cargado');

        $this->em->persist($articulo);
        $this->em->flush();
    }

    public function getArticulos($reportero)
    {
        $articulosEntity = $this->em->getRepository('NWPrincipalBundle:ArticuloReportero');
        $articulos = $articulosEntity->findAll();

        foreach ($articulos as $index => $articulo)
        {
            $articulos[$index] = $articulo->getValues();
        }

        return $articulos;
    }

    public function enviarArticulo($id)
    {
        $articulosEntity = $this->em->getRepository('NWPrincipalBundle:ArticuloReportero');
        $articulo = $articulosEntity->find($id);

        $articulo->setEstatus('En revisión');

        $this->em->persist($articulo);
        $this->em->flush();
    }

    public function eliminarArticulo($id)
    {
        $articulosEntity = $this->em->getRepository('NWPrincipalBundle:ArticuloReportero');
        $articulo = $articulosEntity->find($id);
        
        $this->em->remove($articulo);
        $this->em->flush();
    }
 
}