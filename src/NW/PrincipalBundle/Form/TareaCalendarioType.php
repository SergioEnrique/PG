<?php

namespace NW\PrincipalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TareaCalendarioType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categoriaId', 'choice', array('choices' => $options['categorias'], 'multiple' => false))
            ->add('compromiso', 'text')
            ->add('descripcion', 'textarea')
            ->add('hora', 'choice', array('choices' => $options['horas'], 'multiple' => false, 'mapped' => false))
            ->add('minuto', 'choice', array('choices' => $options['minutos'], 'multiple' => false, 'mapped' => false))
            ->add('contactoNombre', 'text', array('required' => false))
            ->add('contactoTelefono', 'text', array('required' => false))
            ->add('contactoEmail', 'text', array('required' => false))
            ->add('contactoDireccion', 'text', array('required' => false))
            ->add('Agregar', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NW\PrincipalBundle\Entity\TareaCalendario',
            'categorias' => array('1' => 'Categoria Uno', '2' => 'Categoria Dos'),
            'horas' => array('' => 'Hora', '00', '01', '02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'),
            'minutos' => array('' => 'Min', "0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50","51","52","53","54","55","56","57","58","59"),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nw_principalbundle_tareacalendario';
    }
}
