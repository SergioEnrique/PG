<?php
// src/NW/PrincipalBundle/Twig/TodayExtension.php
namespace NW\PrincipalBundle\Twig;

use Symfony\Component\HttpFoundation\Request;

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
        $lang = $this->translator->getLocale();

        $arrayMeses = array($this->translator->trans('Enero'), $this->translator->trans('Febrero'), $this->translator->trans('Marzo'), $this->translator->trans('Abril'), $this->translator->trans('Mayo'), $this->translator->trans('Junio'), $this->translator->trans('Julio'), $this->translator->trans('Agosto'), $this->translator->trans('Septiembre'), $this->translator->trans('Octubre'), $this->translator->trans('Noviembre'), $this->translator->trans('Diciembre'));
        $arrayDias = array($this->translator->trans('Domingo'), $this->translator->trans('Lunes'), $this->translator->trans('Martes'), $this->translator->trans('Miércoles'), $this->translator->trans('Jueves'), $this->translator->trans('Viernes'), $this->translator->trans('Sabado'));
        // echo $arrayDias[date('w')].", ".date('d')." de ".$arrayMeses[date('m')-1]." de ".date('Y');
        if($lang == 'es'){
            return date('j')." de ".$arrayMeses[date('m')-1]." de ".date('Y');
        }
        else{
            return $arrayMeses[date('m')-1]." ".date('j').", ".date('Y');
        }
    }

    public function getName()
    {
        return 'today_extension';
    }
}