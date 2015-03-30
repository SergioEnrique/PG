<?php

namespace PG\PartyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BucketGiftType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo', 'text', array('label' => 'Evento'))
            ->add('date', 'text', array('label' => 'Fecha', 'mapped' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PG\PartyBundle\Entity\BucketGift'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pg_partybundle_bucketgift';
    }
}
