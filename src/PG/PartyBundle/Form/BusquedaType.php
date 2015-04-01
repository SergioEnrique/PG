<?php
namespace PG\PartyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BusquedaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre', 'text', array('required' => false));
        $builder->add('apellidos', 'text', array('required' => false));
        $builder->add('evento', 'text', array('required' => false));
        $builder->add('partyid', 'text', array('required' => false, 'label'=> 'partyID'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'busqueda';
    }
}