<?php

namespace MensajeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MensajeType extends AbstractType 
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('asunto', null, array('label' => 'Asunto'))
            ->add('descripcion', null, array('label' => 'Texto', 'attr' => array('class' => 'ckeditor') ))
            ->add('estado')
            ->add('fechaEnvio')
            ->add('destinatario','entity', array('label' => 'Enviar a:',
            'class' => 'UserBundle:User', 'property' => 'username',
            'multiple' => true, 'expanded' => false, 'mapped' => false))
            ->add('remitente')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MensajeBundle\Entity\Mensaje'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mensajebundle_mensaje';
    }
}
