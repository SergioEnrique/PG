<?php

namespace NW\PrincipalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticuloReporteroType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categoriaId', 'choice', array('choices' => $options['categorias'], 'multiple' => false))
            ->add('titulo', 'text')
            ->add('contenido', 'textarea')
            ->add('Cargar', 'submit')
        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NW\PrincipalBundle\Entity\ArticuloReportero',
            'categorias' => array('1' => 'Fuck', '2' => 'this'),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nw_principalbundle_articuloreportero';
    }
}
