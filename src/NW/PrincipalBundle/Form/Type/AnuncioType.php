<?php
namespace NW\PrincipalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AnuncioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('webpage', 'text');
        $builder->add('Agregar', 'submit');
        $builder->add('file', 'file');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'anuncio';
    }
}