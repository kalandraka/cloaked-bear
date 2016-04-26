<?php

namespace Buseta\EmpleadosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmpleadoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombres', 'text', array(
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('apellidos', 'text', array(
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('cedula', 'text', array(
                'required' => true,
                'label' => 'Cédula',
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('genero', 'choice', array(
                'label' => 'Género',
                'choices' => array(
                    'm' => 'Masculino',
                    'f' => 'Femenino'
                )
            ))
            ->add('estadoCivil','entity',array(
                'class' => 'BusetaNomencladorBundle:EstadoCivil',
                'placeholder' => '---Seleccione---',
                'label' => 'Estado Civil',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('nacionalidad','entity',array(
                'class' => 'BusetaNomencladorBundle:Nacionalidad',
                'placeholder' => '---Seleccione---',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('direccion', 'textarea', array(
                'required' => true,
                'label' => 'Dirección',
                'attr'   => array(
                    'class' => 'form-control',
                )
            ))
            ->add('telefono', 'text', array(
                'required' => true,
                'label' => 'Teléfono',
                'attr'   => array(
                    'class' => 'form-control',
                )
            ))
            ->add('fechaNacimiento', 'date', array(
                'widget' => 'single_text',
                'label' => 'Fecha de Nacimiento',
                'required' => false,
                'format'  => 'dd/MM/yyyy',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('pin')
            ->add('codigoBarras')
            ->add('hhrr')
            ->add('tipoEmpleado','entity',array(
                'label'=>'Tipo de Empleado',
                'class' => 'BusetaEmpleadosBundle:TipoEmpleado',
                'placeholder' => '---Seleccione---',
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\EmpleadosBundle\Entity\Empleado'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_empleadosbundle_empleado';
    }
}
