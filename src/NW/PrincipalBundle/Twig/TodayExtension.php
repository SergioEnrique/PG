<?php
// src/NW/PrincipalBundle/Twig/TodayExtension.php
namespace NW\PrincipalBundle\Twig;

class TodayExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('today', array($this, 'today')),
        );
    }

    public function today()
    {
        $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $arrayDias = array( 'Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado');
        // echo $arrayDias[date('w')].", ".date('d')." de ".$arrayMeses[date('m')-1]." de ".date('Y');
        return date('j')." de ".$arrayMeses[date('m')-1]." de ".date('Y');
    }

    public function getName()
    {
        return 'today_extension';
    }
}