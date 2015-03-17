<?php
// src/NW/PrincipalBundle/Form/Type/DatosBodaType.php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class DatosBodaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ceremonia', 'text');
        $builder->add('ceremonia_direccion', 'textarea');
        $builder->add('recepcion', 'text');
        $builder->add('recepcion_direccion', 'textarea');
    }

    public function getName()
    {
        return 'DatosBoda';
    }
}