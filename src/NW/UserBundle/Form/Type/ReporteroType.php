<?php
// src/NW/UserBundle/Form/Type/NovioType.php
namespace NW\UserBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
 
class ReporteroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombreRazon', 'text');
        $builder->add('apellidoPaterno', 'text', array('required' => false));
        $builder->add('apellidoMaterno', 'text', array('required' => false));
        $builder->add('email', 'email');
        $builder->add('lada', 'text', array('max_length' => 3));
        $builder->add('telefono', 'text', array('max_length' => 8));
        $builder->add('celular', 'text', array('max_length' => 10));
        $builder->add('rfc', 'text');
        $builder->add('direccion', 'text');
        $builder->add('pais', 'choice', array(
                'choices' => array(
                    'MX' => 'México',),
                'multiple' => false, 'mapped' => false,));
        $builder->add('estadoId', 'choice', array(
                'choices' => array(
                    '1'   => 'Aguascalientes',
                    '2'   => 'Baja California',
                    '3'   => 'Baja California Sur',
                    '4'   => 'Campeche',
                    '5'   => 'Chiapas',
                    '6'   => 'Chihuahua',
                    '7'   => 'Coahuila',
                    '8'   => 'Colima',
                    '9'   => 'Distrito Federal',
                    '10'   => 'Durango',
                    '11'   => 'Estado de México',
                    '12'   => 'Guanajuato',
                    '13'   => 'Guerrero',
                    '14'   => 'Hidalgo',
                    '15'   => 'Jalisco',
                    '16'   => 'Michoacán',
                    '17'   => 'Morelos',
                    '18'   => 'Nayarit',
                    '19'   => 'Nuevo León',
                    '20'   => 'Oaxaca',
                    '21'   => 'Puebla',
                    '22'   => 'Querétaro',
                    '23'   => 'Quintana Roo',
                    '24'   => 'San Luis Potosí',
                    '25'   => 'Sinaloa',
                    '26'   => 'Sonora',
                    '27'   => 'Tabasco',
                    '28'   => 'Tamaulipas',
                    '29'   => 'Tlaxcala',
                    '30'   => 'Veracruz',
                    '31'   => 'Yucatán',
                    '32'   => 'Zacatecas',),
                'multiple'  => false,));
        $builder->add('ciudad', 'text', array('required' => true));
        $builder->add('cp', 'text', array('max_length' => 5));
        
        $builder->add('userName', 'text', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()));
        $builder->add('userPass', 'password', array('mapped' => false, 'required'  => true, 'constraints' => array(new NotBlank(), new Length(array('min' => 8)))));
        
        $builder->add('terminosCondiciones', 'checkbox', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()));
        $builder->add('terminosPrivacidad', 'checkbox', array('mapped' => false, 'required'  => true, 'constraints' => new NotBlank()));
        $builder->add('Aceptar', 'submit');
    }

 	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NW\UserBundle\Entity\Reportero'
        ));
    }

    public function getName()
    {
        return 'reporteroForm';
    }
}