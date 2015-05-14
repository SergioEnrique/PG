<?php

namespace PG\PartyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BucketGiftType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo', 'text', array('label' => 'Evento', 'constraints' => new NotBlank()))
            ->add('date', 'text', array('label' => 'Fecha', 'mapped' => false, 'constraints' => new NotBlank()))
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
