<?php

namespace Buseta\BodegaBundle\Form\Filtro;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class BusquedaPedidoCompraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero_documento', 'text', array(
                'required' => false,
                'label'  => 'Nro.Documento',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('tercero','entity',array(
                'class' => 'BusetaBodegaBundle:Tercero',
                'query_builder' => function(EntityRepository $er){
                    $qb = $er->createQueryBuilder('t');
                    return $qb->join('t.proveedor', 'proveedor')
                        ->where($qb->expr()->isNotNull('proveedor.id'));
                },
                'placeholder' => '---Seleccione proveedor---',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('almacen','entity',array(
                'class' => 'BusetaBodegaBundle:Bodega',
                'label' => 'Bodega',
                'placeholder' => '---Seleccione---',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('moneda', 'entity', array(
                'class' => 'BusetaNomencladorBundle:Moneda',
                'placeholder' => '---Seleccione tipo de moneda---',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('forma_pago', 'entity', array(
                'class' => 'BusetaNomencladorBundle:FormaPago',
                'label' => 'Forma de Pago',
                'placeholder' => '---Seleccione forma de pago---',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('condiciones_pago', 'entity', array(
                'class' => 'BusetaTallerBundle:CondicionesPago',
                'label' => 'Condiciones de Pago',
                'placeholder' => '---Seleccione condiciones de pago---',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('importe_total_lineas', 'text', array(
                'required' => false,
                'label'  => 'Importe total líneas',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('importe_total', 'text', array(
                'required' => false,
                'label'  => 'Importe total',
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
        return 'data_busqueda_pedido_compra_type';
    }
}
