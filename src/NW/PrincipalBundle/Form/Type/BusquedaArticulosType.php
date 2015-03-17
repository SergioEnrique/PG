<?php
// src/NW/PrincipalBundle/Form/Type/BusquedaArticulosType.php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BusquedaArticulosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('categorias', 'choice', array(
                'choices' => array(
                    ''    => '¿Qué buscas?',
                    '1'   => 'Jardines',
                    '2'   => 'Salones',
                    '3'   => 'Hoteles',
                    '4'   => 'Playa',
                    '5'   => 'Bebidas',
                    '6'   => 'Vino',
                    '7'   => 'Eventos/Expos',
                    '8'   => 'Promociones',
                    '9'   => 'Iglesias',
                    '10'   => 'Catering (Banquetes)',
                    '11'   => 'Flores',
                    '12'   => 'Música',
                    '13'   => 'Invitaciones',
                    '14'   => 'Tornaboda',
                    '15'   => 'Anillos',
                    '16'   => 'Lunas de miel',
                    '17'   => 'Viajes',
                    '18'   => 'Spas',
                    '19'   => 'Maquillistas',
                    '20'   => 'Estilistas',
                    '21'   => 'Fotógrafos',
                    '22'   => 'Vestidos de novia',
                    '23'   => 'Trajes de novio',
                    '24'   => 'Sastres',
                    '25'   => 'Fuegos artificiales',
                    '26'   => 'Cenas de pareja',),
                'multiple' => false,
                'required' => false,));
        $builder->add('estado', 'choice', array(
                'choices' => array(
                    ''    => '¿Dónde Vives?',
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
                'multiple' => false,
                'required' => false,));
        $builder->add('otro', 'text', array('required' => false));
        $builder->add('proveedor', 'text', array('required' => false));
        $builder->add('Buscar', 'submit');
    }

    public function getName()
    {
        return 'BusquedaArticulos';
    }
}