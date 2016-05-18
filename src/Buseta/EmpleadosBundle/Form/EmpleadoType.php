<?php

namespace Buseta\EmpleadosBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ))
            ->add('apellidos', 'text', array(
                'required' => true,
            ))
            ->add('cedula', 'text', array(
                'required' => true,
                'label' => 'Cédula',
            ))
            ->add('genero', 'choice', array(
                'label' => 'Género',
                'choices' => array(
                    'm' => 'Masculino',
                    'f' => 'Femenino'
                ),
                'required' => true,
                'placeholder' => '---Seleccione---',
            ))
            ->add('estadoCivil','entity',array(
                'class' => 'BusetaNomencladorBundle:EstadoCivil',
                'placeholder' => '---Seleccione---',
                'label' => 'Estado Civil',
                'required' => true,
            ))
            ->add('nacionalidad','entity',array(
                'class' => 'BusetaNomencladorBundle:Nacionalidad',
                'placeholder' => '---Seleccione---',
                'required' => true,
            ))
            ->add('direccion', 'textarea', array(
                'required' => false,
                'label' => 'Dirección',
            ))
            ->add('telefono', 'text', array(
                'required' => false,
                'label' => 'Teléfono',
            ))
            ->add('fechaNacimiento', 'date', array(
                'widget' => 'single_text',
                'label' => 'Fecha de Nacimiento',
                'required' => false,
                'format'  => 'dd/MM/yyyy',
            ))
            ->add('pin')
            ->add('codigoBarras')
            ->add('hhrr', 'entity', array(
                'class' => 'HatueySoft\SecurityBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('user')
                        ->where('user.id NOT IN (SELECT u.id FROM HatueySoft\SecurityBundle\Entity\User u JOIN Buseta\EmpleadosBundle\Entity\Empleado e WITH e.hhrr = u.id WHERE e.hhrr IS NOT NULL)');
                },
                'required' => false,
                'placeholder' => '---Seleccione---',
            ))
            ->add('tipoEmpleado','entity',array(
                'label'=>'Tipo de Empleado',
                'class' => 'BusetaNomencladorBundle:TipoEmpleado',
                'placeholder' => '---Seleccione---',
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
        return 'busetaempleados_empleado_type';
    }
}
