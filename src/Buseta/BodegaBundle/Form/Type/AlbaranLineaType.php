<?php

namespace Buseta\BodegaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlbaranLineaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPresetData'));

        $builder
            ->add('producto', 'entity', array(
                'class' => 'BusetaBodegaBundle:Producto',
                'placeholder' => '---Seleccione producto---',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('valorAtributos', 'text', array(
                'required' => true,
                'label' => 'Valor atributos',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('cantidadMovida', 'integer', array(
                'required' => true,
                'label' => 'Cantidad movida',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))->add('seriales', 'textarea', array(
                'required' => false,
                'label' => 'Seriales',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ));
    }

    public function onPresetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var \Buseta\BodegaBundle\Entity\AlbaranLinea $data */
        $data = $event->getData();
        if ($data !== null) {
            /**
             * @var \Buseta\BodegaBundle\Entity\Producto $producto
             */
            $producto = $data->getProducto();

            if ($producto !== null) {
                $form->add('uom', 'entity', array(
                    'class' => 'BusetaNomencladorBundle:UOM',
                    'data' => $producto->getUom(),
                    'placeholder' => '---Seleccione unidad de medida---',
                    'required' => true,
                    'attr' => array('class' => 'form-control',),
                ));
            } else {
                $form->add('uom', 'entity', array(
                    'class' => 'BusetaNomencladorBundle:UOM',
                    'placeholder' => '---Seleccione unidad de medida---',
                    'required' => true,
                    'attr' => array('class' => 'form-control',),
                ));
            }
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\BodegaBundle\Entity\AlbaranLinea'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_bodegabundle_albaran_linea';
    }
}
