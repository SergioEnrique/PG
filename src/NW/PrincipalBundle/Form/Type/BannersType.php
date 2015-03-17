<?php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BannersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text');
        $builder->add('file', 'file');
        $builder->add('Agregar', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'banners';
    }
}