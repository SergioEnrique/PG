<?php
// src/NW/PrincipalBundle/Form/Type/NotasType.php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class NotasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titulo', 'text');
        $builder->add('descripcion', 'textarea');
    }

    public function getName()
    {
        return 'Notas';
    }
}