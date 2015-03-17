<?php
// src/NW/PrincipalBundle/Servicios/BusquedaNovios.php
namespace NW\PrincipalBundle\Servicios;
use NW\UserBundle\Entity\Novios;
use NW\UserBundle\Entity\Novias;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;


class BusquedaNovios
{
    protected $em; // Doctrine Entity Manager
    protected $router; // Para gererar rutas url

    public function __construct($router, $em)
    {
    	$this->em = $em;
    	$this->router = $router;
    }

	public function buscarNovios($formData)
    {
        $resultados = array();
        $parametros = array();

        $novioEntity = $this->em->getRepository('NWUserBundle:Novios');
        $noviaEntity = $this->em->getRepository('NWUserBundle:Novias');
        $bodasEntity = $this->em->getRepository('NWPrincipalBundle:Bodas');

        $query = $novioEntity->createQueryBuilder('o') // Tabla de Novios
            ->innerJoin('o.novia', 'a'); // Join Tabla de Novias
            // ->where('o.nombre LIKE :novioNombre') // Si no se llenó el formulario se buscan todos

        if($formData['novioNombre'])
        {
            $query->orWhere('o.nombre LIKE :novioNombre');
            $query->orWhere('o.sNombre LIKE :novioNombre');
            $parametros['novioNombre'] = '%'.$formData['novioNombre'].'%';
        }
        if($formData['novioApellidoP'])
        {
            $query->orWhere('o.aPaterno LIKE :novioApellidoP');
            $parametros['novioApellidoP'] = '%'.$formData['novioApellidoP'].'%';
        }
        if($formData['novioApellidoM'])
        {
            $query->orWhere('o.aMaterno LIKE :novioApellidoM');
            $parametros['novioApellidoM'] = '%'.$formData['novioApellidoM'].'%';
        }
        if($formData['noviaNombre'])
        {
            $query->orWhere('a.nombre LIKE :noviaNombre');
            $query->orWhere('a.sNombre LIKE :noviaNombre');
            $parametros['noviaNombre'] = '%'.$formData['noviaNombre'].'%';
        }
        if($formData['noviaApellidoP'])
        {
            $query->orWhere('a.aPaterno LIKE :noviaApellidoP');
            $parametros['noviaApellidoP'] = '%'.$formData['noviaApellidoP'].'%';
        }
        if($formData['noviaApellidoM'])
        {
            $query->orWhere('a.aMaterno LIKE :noviaApellidoM');
            $parametros['noviaApellidoM'] = '%'.$formData['noviaApellidoM'].'%';
        }

        $query->setParameters($parametros);
        $result = $query->getQuery()->getResult();

        foreach($result as $index => $value)
        {
            $usuarioId = $result[$index]->getUsuarioId();

            $resultados[$index]['novioNombre'] = $result[$index]->getNombreCompleto();
            $resultados[$index]['noviaNombre'] = $noviaEntity->findOneBy(array('usuarioId' => $usuarioId))->getNombreCompleto();
            $resultados[$index]['usuarioId'] = $usuarioId;

            $Boda = $bodasEntity->findOneBy(array('usuarioId' => $usuarioId));

            if($Boda->hayFechaBoda())
            {
                $resultados[$index]['fechaBoda'] = $Boda->getFechaBoda()->format('d - m - Y');
            }
            else
            {
                $resultados[$index]['fechaBoda'] = 'No se ha definido';
            }
        }

        return $resultados;
    }
 
}