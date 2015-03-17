<?php
// src/NW/UserBundle/Form/Type/RegistroType.php
namespace NW\UserBundle\Form\Type;

use NW\UserBundle\Form\Type\NoviaType;
use NW\UserBundle\Form\Type\NovioType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Se cargan los formularios para el novio y la novia
        $builder->add('novias', new NoviaType());
        $builder->add('novios', new NovioType());

        // Se genera el formulario para el registro de usuarios
        $builder->add('userName', 'text', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()));
        $builder->add('userPass', 'password', array('mapped' => false, 'required'  => true, 'constraints' => array(new NotBlank(), new Length(array('min' => 8)))));
        $builder->add('mismaDireccion', 'checkbox', array('mapped' => false, 'required'  => false));

        $builder->add('codigoPromocion', 'text', array('mapped' => false, 'required'  => false));

        $builder->add('terminosCondiciones', 'checkbox', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()));
        $builder->add('terminosPrivacidad', 'checkbox', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()));
        $builder->add('aceptar', 'submit');
    }

    public function getName()
    {
        return 'registro';
    }
}