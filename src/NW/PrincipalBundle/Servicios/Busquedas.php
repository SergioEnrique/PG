<?php
// src/NW/PrincipalBundle/Servicios/Busquedas.php
namespace NW\PrincipalBundle\Servicios;

class Busquedas
{
    protected $em; // Doctrine Entity Manager
    protected $router; // Para gererar rutas url

    public function __construct($router, $em)
    {
    	$this->em = $em;
    	$this->router = $router;
    }

	public function busquedaGeneral($busqueda)
    {
        $resultados = array();
        $resultados["parejas"] = $this->buscarNovios($busqueda);
        $resultados["articulos"] = $this->buscarArticulos($busqueda);
        return $resultados;
    }

    public function buscarNovios($busqueda)
    {

        // Se buscará cada una de las palabras escritas en el campo de búsqueda
        $busquedas = explode(" ", $busqueda);

        // Se obtienen las entidades
        $novioEntity = $this->em->getRepository('NWUserBundle:Novios');
        $noviaEntity = $this->em->getRepository('NWUserBundle:Novias');
        $bodasEntity = $this->em->getRepository('NWPrincipalBundle:Bodas');

        // Arreglos para hacer la búsqueda
        $resultados = array();
        $parametros = array();

        // Se crea una consulta para doctrine
        $query = $novioEntity->createQueryBuilder('o') // Tabla de Novios
            ->innerJoin('o.novia', 'a'); // Join Tabla de Novias

        // Cláusulas where para refinar la búsqueda por cada palabra
        foreach ($busquedas as $key => $palabra) {
            $query->orWhere('o.nombre LIKE :busqueda'.$key);
            $query->orWhere('o.sNombre LIKE :busqueda'.$key);
            $query->orWhere('o.aPaterno LIKE :busqueda'.$key);
            $query->orWhere('o.aMaterno LIKE :busqueda'.$key);
            $query->orWhere('a.nombre LIKE :busqueda'.$key);
            $query->orWhere('a.sNombre LIKE :busqueda'.$key);
            $query->orWhere('a.aPaterno LIKE :busqueda'.$key);
            $query->orWhere('a.aMaterno LIKE :busqueda'.$key);

            $parametros['busqueda'.$key] = '%'.$palabra.'%';
        }
            
        // Se hace un set de los parámetros a buscar y se realiza la búsqueda
        $query->setParameters($parametros);
        $result = $query->getQuery()->getResult();

        // Se recorre la búsqueda para asignar parejas y bodas
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

    public function buscarArticulos($busqueda)
    {
        // Obtener repositorio
        $articulosEntity = $this->em->getRepository('NWPrincipalBundle:Articulos'); // Entidad de Artículos
         
        $query = $articulosEntity->createQueryBuilder('a')
            ->where('a.nombre LIKE :buscar')
            ->orWhere('a.descripcion LIKE :buscar')
            ->orderBy('a.nombre', 'ASC')
            ->setParameter('buscar', '%'.$busqueda.'%')
            ->getQuery();
         
        $resultados = $query->getResult();

        return $this->articulosToArray($resultados);
    }

    public function articulosToArray($resultados)
    {
        // Si existen resultados que mostrar, éstos convierten en array sus contenidos desde objetos
        if($resultados)
        {
            // Convirtiendo los resultados en arrays
            foreach($resultados as $index=>$value)
            {
                $objetoenArray=$resultados[$index]->getValues();
                $resultados[$index]=$objetoenArray;

                foreach($resultados[$index]['fotos'] as $indice=>$valor)
                {
                    $objeto2enArray=$resultados[$index]['fotos'][$indice]->getValues(false);
                    $resultados[$index]['fotos'][$indice]=$objeto2enArray;
                    $resultados[$index]['articulo'] = true;
                }
            }
        }

        return $resultados;
    }
 
}