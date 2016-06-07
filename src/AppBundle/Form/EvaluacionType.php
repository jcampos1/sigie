<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Form\Listener\AddExcercisesFieldSubscriber;

class EvaluacionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('asignatura', 'choice', array('label' => 'Asignatura', 'choices'   => array('EDI' => 'EDI', 'EDII' => 'EDII')))
            ->add('tipo', 'choice', array('label' => 'Componente', 'choices'   => array(
                'teorico' => 'Teórico','practico' => 'Práctico',
                 'teorico-practico' => 'Teórico-Práctico') ))
            ->add('Complejidad', 'entity', array('label' => 'Complejidad(es)','class' => 'AppBundle:Complejidad',
            'property' => 'nombre', 'multiple' => true, 'expanded' => false,
            'mapped' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Evaluacion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_evaluacion';
    }
}
