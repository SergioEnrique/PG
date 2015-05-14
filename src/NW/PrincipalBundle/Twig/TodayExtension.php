<?php
// src/NW/PrincipalBundle/Twig/TodayExtension.php
namespace NW\PrincipalBundle\Twig;

class TodayExtension extends \Twig_Extension
{
    protected $translator;

    public function __construct($translator)
    {
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('today', array($this, 'today')),
        );
    }

    public function today()
    {
        $arrayMeses = array($this->translator->trans('Enero'), 'Febrero', 'Marzo', 'Abril', $this->translator->trans('Mayo'), 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $arrayDias = array( 'Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado');
        // echo $arrayDias[date('w')].", ".date('d')." de ".$arrayMeses[date('m')-1]." de ".date('Y');
        return date('j')." de ".$arrayMeses[date('m')-1]." de ".date('Y');
    }

    public function getName()
    {
        return 'today_extension';
    }
}