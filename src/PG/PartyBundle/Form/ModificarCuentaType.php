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
 		$builder->add('moneda', 'choice', array(
            'multiple' => false,
            'choices' => array(
                'ARS' => "ARS - Peso Argentino",
                'AUD' => "AUD - Australian Dollar",
                'BOB' => "BOB - Boliviano",
                'BRL' => "BRL - Real Brasileiro",
                'CAD' => "CAD - Canadian Dollar",
                'CLP' => "CLP - Peso Chileno",
                'COP' => "COP - Peso Colombiano",
                'DKK' => "DKK - Danish Krone",
                'EUR' => "EUR - Euro",
                'GBP' => "GBP - Great Britain Pound",
                'GTQ' => "GTQ - Quetzal Guatemalteco",
                'HKD' => "HKD - Hong Kong Dollar",
                'MXN' => "MXN - Peso Mexicano",
                'NOK' => "NOK - Norwegian Krone",
                'NZD' => "NZD - Nueva Zelanda Dollar",
                'PEN' => "PEN - Sol Peruano",
                'SEK' => "SEK - Swedish Krone",
                'USD' => "USD - US Dollar",
                'VEF' => "VEF - Bolivar Venezolano",
            ),
        ));
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