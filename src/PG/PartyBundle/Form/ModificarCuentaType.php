<?php
namespace PG\PartyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ModificarCuentaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'text', array('label' => "Correo electrónico"));
        $builder->add('nombre', 'text');
        $builder->add('apellidos', 'text');

        $builder->add('oldPass', "password", array("mapped" => false, "required" => false, "label" => "Contraseña actual"));
        $builder->add('newPass', "password", array("mapped" => false, "required" => false, "label" => "Nueva contraseña"));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NW\UserBundle\Entity\User',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'modificarCuenta';
    }
}