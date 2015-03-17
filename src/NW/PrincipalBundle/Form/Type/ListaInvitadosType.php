<?php
// src/NW/PrincipalBundle/Form/Type/ListaInvitadosType.php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class ListaInvitadosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre', 'text');
        $builder->add('familia', 'text');
        $builder->add('telefono', 'text');
        $builder->add('Agregar', 'submit');
    }

    public function getName()
    {
        return 'ListaInvitados';
    }
}