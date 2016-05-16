<?php

namespace Buseta\BodegaBundle\Form\Type;

use Buseta\BodegaBundle\Entity\Producto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;

class NecesidadMaterialLineaType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'preSubmit'));
        $builder
            ->add('linea', 'integer', array(
                'required' => true,
                'label'  => 'Línea',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('producto', 'entity', array(
                'class' => 'BusetaBodegaBundle:Producto',
                'placeholder' => '---Filtrar por código o nombre---',
                'required' => true,
                'choices' => array(),
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('cantidad_pedido', 'integer', array(
                'required' => true,
                'label'  => 'Cantidad de pedido',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('uom', 'entity', array(
                'class' => 'BusetaNomencladorBundle:UOM',
                'placeholder' => '---Seleccione---',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('precio_unitario', 'integer', array(
                'required' => true,
                'label'  => 'Costo unitario',
                'attr'   => array(
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
            ->add('moneda', 'entity', array(
                'class' => 'BusetaNomencladorBundle:Moneda',
                'placeholder' => '---Seleccione---',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('porciento_descuento', 'text', array(
                'required' => false,
                'label'  => '% Descuento',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('importe_linea', 'text', array(
                'required' => true,
                'read_only' => true,
                'label'  => 'Importe línea',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\BodegaBundle\Entity\NecesidadMaterialLinea',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_bodegabundle_necesidad_material_linea';
    }

    public function preSubmit(FormEvent $formEvent)
    {
        $form = $formEvent->getForm();
        $data = $formEvent->getData();
        if ($data !== null) {
            $id = $data['producto'];
            $form->add('producto', 'entity', array(
                'class' => 'BusetaBodegaBundle:Producto',
                'placeholder' => '---Filtrar por código o nombre---',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                ),
                'query_builder' => function (EntityRepository $repository) use ($id) {
                    $qb = $repository->createQueryBuilder('p');
                    $qb->Where($qb->expr()->eq(':id', 'p.id'))
                        ->setParameter('id', $id);

                    return $qb;
                },
            ));
        }
    }
}
