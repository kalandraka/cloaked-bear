<?php
namespace Buseta\BusesBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoferFilter extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombres', 'text', array(
                'required' => false,
                'attr'   => array(
                    'class' => 'form-control',
                )
            ))
            ->add('apellidos', 'text', array(
                'required' => false,
                'attr'   => array(
                    'class' => 'form-control',
                )
            ))
            ->add('cedula', 'text', array(
                'required' => false,
                'label' => 'Cédula',
                'attr'   => array(
                    'class' => 'form-control',
                )
            ))
            ->add('estadoCivil','entity',array(
                'required' => false,
                'class' => 'BusetaNomencladorBundle:EstadoCivil',
                'placeholder' => '---Seleccione---',
                'label' => 'Estado Civil',
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('nacionalidad','entity',array(
                'required' => false,
                'class' => 'BusetaNomencladorBundle:Nacionalidad',
                'placeholder' => '---Seleccione---',
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\BusesBundle\Form\Model\ChoferFilterModel',
            'method' => 'GET',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_chofer_filter';
    }
}
