<?php

namespace Buseta\BodegaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PedidoCompraLineaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPresetData'));

        $builder
            ->add('linea', 'integer', array(
                'required' => true,
                'label' => 'Línea',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('producto', 'entity', array(
                'class' => 'BusetaBodegaBundle:Producto',
                'placeholder' => '---Seleccione---',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('cantidad_pedido', 'integer', array(
                'required' => true,
                'label' => 'Cantidad de pedido',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('precio_unitario', 'number', array(
                'required' => true,
                'scale' => 2,
                'label' => 'Costo unitario',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('impuesto', 'entity', array(
                'class' => 'BusetaTallerBundle:Impuesto',
                'placeholder' => '---Seleccione---',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('porciento_descuento', 'text', array(
                'required' => false,
                'label' => '% Descuento',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('importe_linea', 'text', array(
                'required' => true,
                'read_only' => true,
                'label' => 'Importe línea',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ));
    }

    public function onPresetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var \Buseta\BodegaBundle\Entity\PedidoCompraLinea $data */
        $data = $event->getData();
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\BodegaBundle\Entity\PedidoCompraLinea',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_bodegabundle_pedido_compra_linea';
    }
}
