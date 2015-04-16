<?php
// src/NW/UserBundle/Form/Type/RegistroPartyType.php
namespace NW\UserBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
 
class RegistroPartyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'text');
        $builder->add('nombre', 'text');
        $builder->add('apellidos', 'text');
        $builder->add('moneda', 'choice', array(
                'choices' => array(
                    'AUD' => 'AUD - Australian Dollar',
                    'BRL' => 'BRL - Real Brasileiro',
                    'CAD' => 'CAD - Canadian Dollar',
                    'DKK' => 'DKK - Danish Krone',
                    'EUR' => 'EUR - Euro',
                    'GBP' => 'GBP - Great Britain Pound',
                    'HKD' => 'HKD - Hong Kong Dollar',
                    'MXN' => 'MXN - Peso Mexicano',
                    'NOK' => 'NOK - Norwegian Krone',
                    'NZD' => 'NZD - Nueva Zelanda Dollar',
                    'SEK' => 'SEK - Swedish Krone',
                    'USD' => 'USD - US Dollar',
                ),
                'multiple'  => false,));
        $builder->add('userPass', 'password', array('mapped' => false, 'required'  => true));
        $builder->add('userPass2', 'password', array('mapped' => false, 'required'  => true));
    }

    public function getName()
    {
        return 'registroPartyForm';
    }
}