<?php
// src/NW/PrincipalBundle/Servicios/MesaRegalos.php
namespace NW\PrincipalBundle\Servicios;

class MesaRegalos
{
    protected $em; // Doctrine Entity Manager
    protected $router; // Para gererar rutas url
    protected $formFactory; // Para crear formularios

    private $rangoPrecios = array(
        array('valor' => 0,     'nombre' => 'Elige un rango'),
        array('valor' => 0,     'nombre' => '$1 a $100'),
        array('valor' => 10,   'nombre' => '$101 a $500'),
        array('valor' => 500,   'nombre' => '$501 a $1000'),
        array('valor' => 1000,  'nombre' => '$1001 a $2000'),
        array('valor' => 2000,  'nombre' => '$2001 a $5000'),
        array('valor' => 5000,  'nombre' => '$5001 a $15000'),
        array('valor' => 15000, 'nombre' => 'Más de $15000')
    );

    // Servicios externos
    public function __construct($router, $em, $formFactory)
    {
    	$this->em = $em;
    	$this->router = $router;
        $this->formFactory = $formFactory;
    }

	public function getMesaRegalos($usuarioId, $categoria, $precioArticulo, $precioParte)
    {
        $mesaEntity = $this->em->getRepository('NWPrincipalBundle:MesaRegalos');
        //$mesa = $mesaEntity->findBy(array('usuarioId' => $usuarioId));

        $parametros = array();
        $parametros['usuarioId'] = $usuarioId;

        $query = $mesaEntity->createQueryBuilder('m')
            ->where('m.usuarioId = :usuarioId');

        if($categoria)
        {
            $query->andWhere('m.categoria = :categoria');
            $parametros['categoria'] = $categoria;
        }
        if($precioArticulo)
        {
            $query->andWhere('m.precioTotal > :precioArticuloInferior AND m.precioTotal <= :precioArticuloSuperior');
            $parametros['precioArticuloInferior'] = $this->rangoPrecios[$precioArticulo]['valor'];

            if(array_key_exists($precioArticulo+1, $this->rangoPrecios))
            {
                $parametros['precioArticuloSuperior'] = $this->rangoPrecios[$precioArticulo+1]['valor'];
            }
            else
            {
                $parametros['precioArticuloSuperior'] = 999999999;
            }
        }
        if($precioParte)
        {
            $query->andWhere('m.precioTotal/m.horcruxes > :precioParteInferior AND m.precioTotal/m.horcruxes <= :precioParteSuperior');
            $parametros['precioParteInferior'] = $this->rangoPrecios[$precioParte]['valor'];

            if(array_key_exists($precioParte+1, $this->rangoPrecios))
            {
                $parametros['precioParteSuperior'] = $this->rangoPrecios[$precioParte+1]['valor'];
            }
            else
            {
                $parametros['precioParteSuperior'] = 999999999;
            }
        }

        $query->setParameters($parametros);
        $mesa = $query->getQuery()->getResult();

        if($mesa)
        {
            $articulos = array();
            foreach($mesa as $key => $value)
            {
                $articulos[$key] = $value->getValues();

                $number = 1234.56;
                setlocale(LC_MONETARY,"en_US");

                // Precio por parte obtener
                $precioParteTemporal = $articulos[$key]['precioTotal']/$articulos[$key]['horcruxes'];
                $articulos[$key]['precioParte'] = number_format($precioParteTemporal, 2,'.', ',');

                // Precio total poner comas y puntos
                $precioTotalTemporal = $articulos[$key]['precioTotal'];
                $articulos[$key]['precioTotal'] = number_format($precioTotalTemporal, 2,'.', ',');
            }
        }
        else
        {
            $articulos = false;
        }

        return $articulos;
    }

    public function getNovios($usuarioId)
    {
        $noviaEntity = $this->em->getRepository('NWUserBundle:Novias');
        $novioEntity = $this->em->getRepository('NWUserBundle:Novios');

        $novia = $noviaEntity->findOneBy(array('usuarioId' => $usuarioId));
        $novio = $novioEntity->findOneBy(array('usuarioId' => $usuarioId));

        return $novia->getNombre().' '.$novia->getAPaterno().' y '.$novio->getNombre().' '.$novio->getAPaterno();
    }

    public function getFormFiltrar()
    {
        $catRegalosEntity = $this->em->getRepository('NWPrincipalBundle:CatRegalos');
        $categorias = $catRegalosEntity->findAll();

        $categoria[0] = 'Categoría';
        foreach($categorias as $key => $value)
        {
            $categoria[$key+1] = $categorias[$key]->getCategoriaName();
        }

        foreach($this->rangoPrecios as $key => $value)
        {
            $rangoPrecios[$key] = $value['nombre'];
        }

        $formFiltrar = $this->formFactory->createBuilder('form')
            ->add('categoria', 'choice', array('choices' => $categoria, 'multiple'  => false,))
            ->add('precioArticulo', 'choice', array('choices' => $rangoPrecios, 'multiple'  => false,))
            ->add('precioParte', 'choice', array('choices' => $rangoPrecios, 'multiple'  => false,))
            ->add('Filtrar', 'submit')
            ->getForm();

        return $formFiltrar;
    }
 
}