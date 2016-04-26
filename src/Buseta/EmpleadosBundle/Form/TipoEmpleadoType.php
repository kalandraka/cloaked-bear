<?php

namespace Buseta\EmpleadosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TipoEmpleadoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'text', array(
                'label' => 'Tipo de Empleado'
            ))
            ->add('descripcion', 'textarea', array(
                'label' => 'Descripción',
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\EmpleadosBundle\Entity\TipoEmpleado'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_empleadosbundle_tipoempleado';
    }
}
