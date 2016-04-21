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
            ->add('nombres')
            ->add('apellidos')
            ->add('cedula')
            ->add('genero')
            ->add('estadoCivil')
            ->add('nacionalidad')
            ->add('direccion')
            ->add('telefono')
            ->add('fechaNacimiento')
            ->add('pin')
            ->add('codigoBarras')
            ->add('hhrr')
            ->add('tipoEmpleado')
        ;
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
