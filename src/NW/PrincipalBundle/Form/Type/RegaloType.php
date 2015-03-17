<?php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegaloType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('regalo', 'text');
        $builder->add('descripcion', 'textarea');
        $builder->add('precioTotal', 'integer');
        //$builder->add('absorberComision', 'checkbox', array('required'  => false)); // YA NO SE USA
        $builder->add('cantidad', 'integer');
        $builder->add('horcruxes', 'integer');
        $builder->add('categoria', 'choice', array('choices' => array(
			'1' => 'Electrodomésticos',
			'2' => 'Cocina',
			'3' => 'Línea blanca',
            '4' => 'Hogar',
            '5' => 'Recámara',
            '6' => 'Niños',
            '7' => 'Viajes',
            '8' => 'Luna de miel',
            '9' => 'Muebles',
            '10' => 'Jardín',
            '11' => 'Mesa',
            '12' => 'Decoración',
            '13' => 'Electrónica',
            '14' => 'Televisión',
            '15' => 'Sonido',
            '16' => 'Otros'
			), 'multiple'  => false,));
        $builder->add('Agregar', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'regalo';
    }
}