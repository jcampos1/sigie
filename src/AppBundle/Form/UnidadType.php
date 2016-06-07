<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UnidadType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', null, array('label' => 'Nombre'))
            ->add('asignatura', 'choice', array('label' => 'Asignatura', 'choices'   => array('EDI' => 'EDI', 'EDII' => 'EDII')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Unidad'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_unidad';
    }
}
