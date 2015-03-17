<?php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProveedorPublicoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombreComercial', 'text');
        $builder->add('descripcion', 'textarea');
        $builder->add('file', 'file', array('required' => false));
        $builder->add('fileGaleria', 'file', array('required' => false));
        $builder->add('terminosCondiciones', 'checkbox', array('mapped' => false, 'required'  => true));
        $builder->add('terminosPrivacidad', 'checkbox', array('mapped' => false, 'required'  => true));
        $builder->add('Actualizar', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'proveedorPublico';
    }
}