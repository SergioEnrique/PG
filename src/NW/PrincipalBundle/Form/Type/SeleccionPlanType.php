<?php
// src/NW/PrincipalBundle/Form/Type/SeleccionPlanType.php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class SeleccionPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plan', 'choice', array('choices' => 
            array(
                'anuncioEspecial' => 'Anuncio Especial',
                'anuncioPlus' => 'Anuncio Plus',
                'basico' => 'Básico',
                'estandar' => 'Estándar',
                'plus' => 'Plus'
            ), 'multiple' => false, 'expanded' => true, 'required' => true, 'empty_data'  => null));
        $builder->add('terminosCondiciones', 'checkbox', array('mapped' => false, 'required'  => true));
        $builder->add('terminosPrivacidad', 'checkbox', array('mapped' => false, 'required'  => true));
        $builder->add('Enviar', 'submit');
    }

    public function getName()
    {
        return 'SeleccionPlan';
    }
}