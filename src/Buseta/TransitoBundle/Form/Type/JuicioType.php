<?php

namespace Buseta\TransitoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JuicioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion', 'text', array(
                'required' => true,
                'label' => 'Descripción',
                'attr'   => array(
                    'class' => 'form-control',
                )
            ))
            ->add('resultado', 'choice', array(
                'label'  => 'Resultado',
                'choices' => array(
                    'NORESPONSABLE' => 'No hubo responsable',
                    'RESPONSABLE' => 'Responsable',
                    'EMPATE' => 'Empate',
                ),
                'required' => true,
                'data' => 'normal',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('responsable', 'choice', array(
                'label'  => 'Responsable',
                'choices' => array(
                    'NOHUBO' => 'No hubo',
                    'CHOFER' => 'Chofer',
                    'TERCERO' => 'Tercero',
                    'EMPRESA' => 'Empresa',
                ),
                'data' => 'normal',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('quienPaga', 'choice', array(
                'label'  => 'Quien solventa',
                'choices' => array(
                    'NADIE' => 'Nadie',
                    'CHOFER' => 'Chofer',
                    'TERCERO' => 'Tercero',
                    'EMPRESA' => 'Empresa',
                    'TALLER' => 'Taller',
                    'SEGURO' => 'Seguro',
                ),
                'data' => 'normal',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('fechaInicio', 'date', array(
                'widget' => 'single_text',
                'label' => 'Fecha Inicial',
                'required' => false,
                'format'  => 'dd/MM/yyyy',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('fechaFin', 'date', array(
                'widget' => 'single_text',
                'label' => 'Fecha Final',
                'required' => false,
                'format'  => 'dd/MM/yyyy',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('importeAbogado', 'number', array(
                'required' => false,
                'label' => 'Importe Abogado',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\TransitoBundle\Entity\Juicio'
        ));
    }

    public function getName()
    {
        return 'transito_juicio';
    }
}
