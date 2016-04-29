<?php
namespace Buseta\TransitoBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultaFilter extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion', 'text', array(
                'label'  => 'Descripción',
                'attr'   => array(
                    'class' => 'form-control',
                )
            ))
            ->add('numArticulo','entity',array(
                'class' => 'BusetaNomencladorBundle:Articulo',
                'placeholder' => '---Seleccione---',
                'label' => 'Artículo',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('fechaInicio', 'date', array(
                'widget' => 'single_text',
                'label'  => 'Desde',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('fechaFin', 'date', array(
                'widget' => 'single_text',
                'label'  => 'Hasta',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('vehiculo','entity',array(
                'class' => 'BusetaBusesBundle:Vehiculo',
                'placeholder' => '---Seleccione---',
                'label' => 'Vehículo',
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('chofer','entity',array(
                'class' => 'BusetaBusesBundle:Chofer',
                'placeholder' => '---Seleccione---',
                'label' => 'Chofer',
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
            'data_class' => 'Buseta\TransitoBundle\Form\Model\MultaFilterModel',
            'method' => 'GET',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_multa_filter';
    }
}
