<?php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BuscarNoviosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('noviaNombre', 'text', array('required' => false));
        $builder->add('noviaApellidoP', 'text', array('required' => false));
        $builder->add('noviaApellidoM', 'text', array('required' => false));
        $builder->add('novioNombre', 'text', array('required' => false));
        $builder->add('novioApellidoP', 'text', array('required' => false));
        $builder->add('novioApellidoM', 'text', array('required' => false));
        $builder->add('Buscar', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'buscarNovios';
    }
}