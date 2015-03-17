<?php
// src/NW/PrincipalBundle/Form/Type/CategoriasType.php
namespace NW\PrincipalBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class CategoriasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('categorias', 'choice', array(
                'choices' => array(
                    ''  => 'Otros',
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
    }

 	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NW\PrincipalBundle\Entity\Categorias'
        ));
    }

    public function getName()
    {
        return 'categorias';
    }
}