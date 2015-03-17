<?php
// src/NW/PrincipalBundle/Servicios/Resenas.php
namespace NW\PrincipalBundle\Servicios;

class Resenas
{
    protected $em; // Doctrine Entity Manager
    protected $router; // Para gererar rutas url

    public function __construct($router, $em)
    {
    	$this->em = $em;
    	$this->router = $router;
    }

	public function getResenas($proveedorId)
    {
        // Variables
        $cantidadResenas = 0;
        $sumaPuntuacion = 0;
        $sumaResenas = array(0, 0, 0, 0, 0); // Suma de puntaje de reseñas
        $porcentajeResena = array();

        $resenasEntity = $this->em->getRepository('NWPrincipalBundle:Resena');
        $resenas = $resenasEntity->findBy(array('proveedorId' => $proveedorId)); // Reseñas en arreglo de objetos

        // Convirtiendo las reseñas en arreglos
        foreach ($resenas as $key => $value) {
            $objeto3enArray = $resenas[$key]->getValues();
            $resenas[$key] = $objeto3enArray;

            switch ($resenas[$key]['puntuacion']) {
                case 1:
                    $sumaResenas[0]+=1;
                    break;
                case 2:
                    $sumaResenas[1]+=1;
                    break;
                case 3:
                    $sumaResenas[2]+=1;
                    break;
                case 4:
                    $sumaResenas[3]+=1;
                    break;
                case 5:
                    $sumaResenas[4]+=1;
                    break;
            }
        }

        foreach($sumaResenas as $key => $value)
        {
            $cantidadResenas+=$value;
            $sumaPuntuacion+=$value*($key+1);
        }

        foreach ($sumaResenas as $key => $value)
        {
            if($cantidadResenas>0)
            {
                $porcentajeResenas[$key] = $sumaResenas[$key] *100/ $cantidadResenas;
            }
            else
            {
                $porcentajeResenas[$key] = 0;
            }
        }

        if($cantidadResenas>0)
        {
            $puntuacionFinal = ceil($sumaPuntuacion / $cantidadResenas); // Redondeo hacia arriba  
        }
        else
        {
            $puntuacionFinal = 0;
        }

        return array('resenas' => $resenas, 'puntuacionFinal' => $puntuacionFinal, 'porcentajeResenas' => $porcentajeResenas);
    }
 
}