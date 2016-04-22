<?php

namespace Buseta\BodegaBundle\Form\Type;

use Buseta\BodegaBundle\Entity\NecesidadMaterial;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Buseta\BodegaBundle\Form\Model\NecesidadMaterialModel;
use Symfony\Component\Validator\Constraints\NotBlank;

class NecesidadMaterialType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var Container
     */
    private $serviceContainer;

    public function __construct(ObjectManager $em, ContainerInterface $serviceContainer)
    {
        $this->em = $em;
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'preSetData'));
        $builder
            ->add('id', 'hidden', array(
                'required' => false,
            ))
            ->add('numero_referencia', 'text', array(
                'required' => false,
                'label'  => 'Nro.Referencia',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('tercero', 'entity', array(
                'class' => 'BusetaBodegaBundle:Tercero',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('t');
                    return $qb
                        ->select('t, proveedor')
                        ->innerJoin('t.proveedor', 'proveedor')
                        ->where($qb->expr()->isNotNull('proveedor.id'))
                        ->orderBy('t.nombres', 'ASC');
                },
                'placeholder' => '---Seleccione---',
                'required' => true,
                'label' => 'Nombre del Proveedor',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('fecha_pedido', 'date', array(
                'widget' => 'single_text',
                'required' => false,
                'format'  => 'dd/MM/yyyy',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('almacen', 'entity', array(
                'class' => 'BusetaBodegaBundle:Bodega',
                'label' => 'Bodega',
                'placeholder' => '---Seleccione---',
                'required' => true,
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
            ->add('forma_pago', 'entity', array(
                'class' => 'BusetaNomencladorBundle:FormaPago',
                'label' => 'Forma de Pago',
                'placeholder' => '---Seleccione---',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('condiciones_pago', 'entity', array(
                'class' => 'BusetaTallerBundle:CondicionesPago',
                'label' => 'Condiciones de Pago',
                'placeholder' => '---Seleccione---',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('descuento', 'number', array(
                'required'  => false,
                'label'     => 'Descuento compra',
                'attr'      => array(
                    'class' => 'form-control',
                )
            ))
            ->add('impuesto', 'entity', array(
                'class'         => 'BusetaTallerBundle:Impuesto',
                'placeholder'   => '---Seleccione---',
                'required'      => false,
                'label'         => 'Impuesto compra',
                'attr'          => array(
                    'class' => 'form-control',
                )
            ))
            ->add('importeCompra', 'number', array(
                'required' => false,
                'label' => 'Importe compra',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('importe_total_lineas', 'number', array(
                'required' => false,
                'label'  => 'Importe total líneas',
                'read_only' => true,
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))

            ->add('importeDescuento', 'number', array(
                'required' => false,
                'read_only' => true,
                'label'  => 'Importe descuento',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('importeImpuesto', 'number', array(
                'required' => false,
                'read_only' => true,
                'label'  => 'Importe impuesto',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('importe_total', 'number', array(
                'required' => false,
                'read_only' => true,
                'label'  => 'Importe total',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('observaciones', 'textarea', array(
                'required' => false,
                'label'  => 'Observaciones',
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
            'data_class' => 'Buseta\BodegaBundle\Form\Model\NecesidadMaterialModel',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bodega_necesidad_material';
    }

    public function preSetData(FormEvent $formEvent)
    {
        $data = $formEvent->getData();
        $form = $formEvent->getForm();
        $sequenceManager = $this->serviceContainer->get('hatuey_soft.sequence.manager');

        if ($data instanceof NecesidadMaterialModel && !$data->getNumeroDocumento()
            && !$sequenceManager->hasSequence('Buseta\BodegaBundle\Entity\NecesidadMaterial')) {
            $form->add('numeroDocumento', 'text', array(
                'required' => true,
                'label'  => 'Nro.Documento',
                'constraints' => array(
                    new NotBlank(),
                )
            ));
        } elseif ($data instanceof NecesidadMaterialModel && $data->getNumeroDocumento()) {
            $form->add('numeroDocumento', 'text', array(
                'required' => true,
                'read_only' => true,
                'data' => $data->getNumeroDocumento(),
                'label'  => 'Nro.Documento',
            ));
        }

    }
}
