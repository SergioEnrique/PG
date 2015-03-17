<?php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NuevaResenaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('puntuacion', 'choice', array('choices' => array(
                     '1'   => '1 Estrella',
                     '2'   => '2 Estrellas',
                     '3'   => '3 Estrellas',
                     '4'   => '4 Estrellas',
                     '5'   => '5 Estrellas',
                    ), 'multiple'  => false,));
        $builder->add('titulo', 'text');
        $builder->add('resena', 'textarea');
        $builder->add('proveedorId', 'hidden');
        $builder->add('Enviar', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NW\PrincipalBundle\Entity\Resena'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nuevaResena';
    }
}