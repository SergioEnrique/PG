<?php
// src/NW/PrincipalBundle/Form/Type/PadrinosType.php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class PadrinosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('padrino', 'text');
        $builder->add('categoria', 'text');
    }

    public function getName()
    {
        return 'Padrinos';
    }
}