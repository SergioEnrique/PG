<?php

namespace NW\PrincipalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SolicitudRetiroType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cuentaPaypal', 'email', array("required" => true))
            ->add('amount', 'number', array("required" => true))
            ->add('Solicitar', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NW\PrincipalBundle\Entity\SolicitudRetiro'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nw_principalbundle_solicitudretiro';
    }
}
