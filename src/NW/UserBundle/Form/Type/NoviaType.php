<?php
// src/NW/UserBundle/Form/Type/Novia.php
namespace NW\UserBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class NoviaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('nombre', 'text');
        $builder->add('sNombre', 'text', array('required' => false));
        $builder->add('aPaterno', 'text');
        $builder->add('aMaterno', 'text', array('required' => false));
        $builder->add('eMail', 'email');
        $builder->add('lada', 'integer', array('max_length' => 3));
        $builder->add('telefono', 'text', array('max_length' => 9));
        $builder->add('celular', 'text', array('max_length' => 12));
        $builder->add('direccion', 'text');
        $builder->add('pais', 'choice', array(
                'choices' => array(
                    'MX' => 'México',),
                'multiple' => false,
                'mapped' => false,));
        $builder->add('estado', 'choice', array(
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
        $builder->add('ciudad', 'text');
        $builder->add('cp', 'integer', array('max_length' => 5));
    }

 	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NW\UserBundle\Entity\Novias'
        ));
    }

    public function getName()
    {
        return 'noviaForm';
    }
}