<?php

namespace Buseta\TransitoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PenalAccidenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nota', 'text', array(
                'required' => true,
                'label' => 'Nota',
                'attr'   => array(
                    'class' => 'form-control',
                )
            ))
            ->add('adjunto', 'text', array(
                'required' => true,
                'label' => 'Adjunto',
                'attr'   => array(
                    'class' => 'form-control',
                )
            ))
            ->add('alerta', 'text', array(
                'required' => true,
                'label' => 'Alerta',
                'attr'   => array(
                    'class' => 'form-control',
                )
            ))
            ->add('fecha', 'date', array(
                'widget' => 'single_text',
                'label' => 'Fecha',
                'required' => false,
                'format'  => 'dd/MM/yyyy',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('fechaExpira', 'date', array(
                'widget' => 'single_text',
                'label' => 'Expira',
                'required' => false,
                'format'  => 'dd/MM/yyyy',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('costo', 'number', array(
                'required' => false,
                'label' => 'Costo',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\TransitoBundle\Entity\PenalAccidente'
        ));
    }

    public function getName()
    {
        return 'transito_penal_accidente';
    }
}
