<?php
// src/NW/PrincipalBundle/Form/Type/FotosArticulosType.php
namespace NW\PrincipalBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class FotosArticulosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file');
    }

 	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NW\PrincipalBundle\Entity\FotosArticulos'
        ));
    }

    public function getName()
    {
        return 'fotosarticulos';
    }
}