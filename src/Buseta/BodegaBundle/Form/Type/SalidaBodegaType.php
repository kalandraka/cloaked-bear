<?php

namespace Buseta\BodegaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Buseta\BodegaBundle\Form\EventListener\AddAlmacenDestinoFieldSubscriber;
use Buseta\BodegaBundle\Form\EventListener\AddAlmacenOrigenFieldSubscriber;
use Doctrine\ORM\EntityRepository;

class SalidaBodegaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $objeto = $builder->getFormFactory();
        $almacenDestino = new AddAlmacenDestinoFieldSubscriber($objeto);
        $builder->addEventSubscriber($almacenDestino);
        $almacenOrigen = new AddAlmacenOrigenFieldSubscriber($objeto);
        $builder->addEventSubscriber($almacenOrigen);

        $builder
            ->add('id', 'hidden', array(
                'required' => false,
            ))
            ->add('responsable', 'entity', array(
                'class' => 'BusetaBodegaBundle:Tercero',
                'required' => true,
                'label'  => 'Responsable',
                'attr'   => array(
                    'class' => 'form-control',
                ),
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('responsable');
                    $qb->join('responsable.usuario', 'usuario')
                        ->andWhere($qb->expr()->isNotNull('usuario.id'));

                    return $qb;
                },
            ))
            ->add('observaciones', 'textarea', array(
                'required' => false,
                'label'  => 'Observaciones',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('tipo_ot', 'choice', array(
                'label'  => 'Tipo de Orden de Trabajo',
                'choices' => array(
                    'rapida' => 'Rápida',
                    'normal' => 'Normal',
                ),
                'required' => true,
                'data' => 'normal',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('centro_costo', 'entity', array(
                'class' => 'BusetaBusesBundle:Autobus',
                'required' => true,
                //'placeholder' => '---Seleccione---',
                'label' => 'Centro de Costo',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('orden_trabajo', 'entity', array(
                'class' => 'BusetaTallerBundle:OrdenTrabajo',
                //'placeholder' => '---Seleccione---',
                'label' => 'Orden de Trabajo',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('fecha', 'date', array(
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Fecha',
                'format'  => 'dd/MM/yyyy',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('controlEntregaMaterial', 'text', array(
                'required' => true,
                'label'  => 'Control Entrega de Materiales',
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\BodegaBundle\Entity\SalidaBodega',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_bodegabundle_salida_bodega';
    }
}
