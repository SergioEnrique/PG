<?php
// src/NW/PrincipalBundle/Form/Type/ChecklistType.php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class ChecklistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tarea', 'text');
        $builder->add('categoria', 'text');
        $builder->add('descripcion', 'textarea');
        $builder->add('Agregar', 'submit');
    }

    public function getName()
    {
        return 'Checklist';
    }
}