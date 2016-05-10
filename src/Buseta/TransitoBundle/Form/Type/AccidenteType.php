<?php

namespace Buseta\TransitoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccidenteType extends AbstractType
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
            ->add('vehiculo','entity',array(
                'class' => 'BusetaBusesBundle:Vehiculo',
                'placeholder' => '---Seleccione---',
                'label' => 'Vehículo',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('chofer','entity',array(
                'class' => 'BusetaBusesBundle:Chofer',
                'placeholder' => '---Seleccione---',
                'label' => 'Chofer',
                'required' => true,
                'attr' => array(
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
            ->add('importe', 'number', array(
                'required' => false,
                'label' => 'Importe',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\TransitoBundle\Entity\Accidente'
        ));
    }

    public function getName()
    {
        return 'transito_accidente';
    }
}
