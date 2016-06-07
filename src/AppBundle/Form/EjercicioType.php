<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Form\Listener\AddStateFieldSubscriber;
class EjercicioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion', null, array('label' => 'Descripción', 'attr' => array('class' => 'ckeditor') ))
            ->add('componente', 'choice', array('label' => 'Componente', 'label' => 'Componente', 'choices'   => array('teorico' => 'Teórico', 'practico' => 'Práctico')))
            ->add('fechaUltimoUso')
            ->add('visibilidad', 'choice', array('label' => 'Visibilidad', 'choices'   => array('privado' => 'Privado', 'publico' => 'Público')))
            ->add('complejidades', 'entity',
             array('label' => 'Complejidad(es)', 'empty_data' => array(null),
                'class' => 'AppBundle:Complejidad',
                'property' => 'nombre', 'multiple' => true, 'expanded' => false, 
                'required' => true, 'attr' => array(
        'class' => 'select_class')))
            ->add('unidad', 'entity', array('label' => 'Unidad', 'class' => 'AppBundle:Unidad','property' => 'nombre'))
            ->add('subtema', 'entity', array('label' => 'Subtema', 'class' => 'AppBundle:Subtema','property' => 'nombre'))
            ->add('solucion', null, array('label' => 'Solución', 'attr' => array('class' => 'ckeditor') ))
            ->add('asignatura', 'choice', array('label' => 'Asignatura', 'choices'   => array('EDI' => 'EDI', 'EDII' => 'EDII')))
        ;

        // Añadimos un EventListener que actualizará el campo subtema
        // para que sus opciones correspondan
        // con la unidad seleccionada por el usuario
        $builder->addEventSubscriber(new AddStateFieldSubscriber());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Ejercicio'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_ejercicio';
    }
}
