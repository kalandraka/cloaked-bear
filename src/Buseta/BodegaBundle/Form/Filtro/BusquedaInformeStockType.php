<?php

namespace Buseta\BodegaBundle\Form\Filtro;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusquedaInformeStockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('almacen', 'entity', array(
                'class' => 'BusetaBodegaBundle:Bodega',
                'placeholder' => '---Seleccione---',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('categoriaProducto', 'entity', array(
                'class' => 'BusetaBodegaBundle:CategoriaProducto',
                'placeholder' => '---Seleccione categoría de producto---',
                'label' => 'Categoría de Producto',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('fecha', 'date', array(
                'widget' => 'single_text',
                'placeholder' => '---Seleccione fecha máxima---',
                'required' => false,
                'format'  => 'dd/MM/yyyy',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'busetabodega_informestock_type';
    }
}
