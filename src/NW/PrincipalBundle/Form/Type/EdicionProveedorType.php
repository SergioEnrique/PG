<?php
// src/NW/PrincipalBundle/Form/Type/EdicionProveedorType.php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class EdicionProveedorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Se cargan los formularios para el novio y la novia
        $builder->add('tipoPersona', 'choice', array('choices' => 
                array('fisica' => 'Persona Física', 'moral' => 'Persona Moral'),
                'multiple' => false, 'expanded' => true, 'required' => true, 'empty_data'  => null));
        $builder->add('nombreRazon', 'text');
        $builder->add('apellidoPaterno', 'text', array('required' => false));
        $builder->add('apellidoMaterno', 'text', array('required' => false));
        $builder->add('rfc', 'text');
        $builder->add('email', 'email');
        $builder->add('lada', 'text', array('max_length' => 3));
        $builder->add('telefono', 'text', array('max_length' => 8));
        $builder->add('celular', 'text', array('max_length' => 10));
        $builder->add('direccion', 'text');
        $builder->add('pais', 'choice', array('choices' => array(
                'MX'   => 'México',
                ), 'mapped' => false, 'multiple'  => false,));
        $builder->add('estado', 'choice', array('choices' => array(
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
                 '32'   => 'Zacatecas',
                ), 'multiple'  => false,));
        $builder->add('ciudad', 'text');
        $builder->add('cp', 'text', array('max_length' => 5));

        $builder->add('terminosCondiciones', 'checkbox', array('mapped' => false, 'required'  => true));
        $builder->add('terminosPrivacidad', 'checkbox', array('mapped' => false, 'required'  => true));
        $builder->add('Enviar', 'submit');
    }

    public function getName()
    {
        return 'EdicionProveedor';
    }
}