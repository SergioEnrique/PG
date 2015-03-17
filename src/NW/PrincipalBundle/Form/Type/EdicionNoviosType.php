<?php
// src/NW/PrincipalBundle/Form/Type/EdicionNoviosType.php
namespace NW\PrincipalBundle\Form\Type;

use NW\UserBundle\Form\Type\NoviaType;
use NW\UserBundle\Form\Type\NovioType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
 
class EdicionNoviosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Se cargan los formularios para el novio y la novia
        $builder->add('novias', new NoviaType());
        $builder->add('novios', new NovioType());

        // Se generan los nuevos campos de formulario
        $builder->add('terminosCondiciones', 'checkbox', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()));
        $builder->add('terminosPrivacidad', 'checkbox', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()));
        $builder->add('Enviar', 'submit');
    }

    public function getName()
    {
        return 'EdicionNovios';
    }
}