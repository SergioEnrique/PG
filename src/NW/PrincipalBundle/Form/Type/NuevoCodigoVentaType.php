<?php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NuevoCodigoVentaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codigo', 'text');
        $builder->add('vendedorId', 'integer', array('required' => false));
        $builder->add('porcentajeVendedor', 'integer', array('required' => false));
        $builder->add('porcentajeDescuento', 'integer', array('required' => false));
        $builder->add('Agregar', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nuevoCodigoVenta';
    }
}