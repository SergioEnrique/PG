<?php

namespace NW\PrincipalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MesaRegalosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('regalo', 'text', array('label' => 'Nombre del gift')) // Nombre del gift
            ->add('descripcion', 'textarea', array('label' => 'Descripción')) // Descripción
            ->add('precioTotal', 'number', array('label' => 'Valor del gift')) // Valor del gift
            ->add('cantidad', 'integer', array('label' => 'Número de gifts')) // Numero de articulos
            ->add('horcruxes', 'integer', array('label' => '¿En cuántas partes quieres dividir este artículo?')) // En cuantas partes quieres dividir este articulo
            ->add('bucketGiftId', 'hidden', array('mapped' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NW\PrincipalBundle\Entity\MesaRegalos',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nw_principalbundle_mesaregalos';
    }
}
