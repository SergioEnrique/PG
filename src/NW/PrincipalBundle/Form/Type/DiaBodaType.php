<?php

namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DiaBodaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fecha', 'datetime');
        $builder->add('Guardar', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'diaBoda';
    }
}