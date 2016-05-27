<?php

namespace Buseta\BusesBundle\Form\Type;

use Buseta\BusesBundle\Form\EventListener\AddMarcaFieldSubscriber;
use Buseta\BusesBundle\Form\EventListener\AddModeloFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AutobusBasicoType extends AbstractType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Constructor
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $objeto = $builder->getFormFactory();
        $modelo = new AddModeloFieldSubscriber($objeto);
        $builder->addEventSubscriber($modelo);
        $marca = new AddMarcaFieldSubscriber($objeto);
        $builder->addEventSubscriber($marca);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $autobus    = $event->getData();
            $form       = $event->getForm();

            if ($autobus && null !== $autobus->getId() || $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $form
                    ->add('kilometraje', 'integer', array(
                        'required' => false,
                    ))
                    ->add('horas', 'integer', array(
                        'required' => false,
                    ));
            }
        });

        $builder
            ->add('id', 'hidden', array(
                'required' => false,
            ))
            ->add('matricula', 'text', array(
                'required' => true,
                'label' => 'Matrícula',
            ))
            ->add('numero', 'text', array(
                'required' => true,
                'label' => 'Número',
            ))
            ->add('numeroChasis', 'text', array(
                'required' => true,
                'label' => 'Número Chasis',
            ))
            ->add('numeroMotor', 'text', array(
                'required' => true,
                'label' => 'Número Motor',
            ))
            ->add('grupo_buses', 'entity', array(
                'class' => 'BusetaBusesBundle:GrupoBuses',
                'label' => 'Grupo Buses',
                'placeholder' => '---Seleccione---',
            ))
            ->add('capacidadTanque', 'number', array(
                'required' => true,
                'label' => 'Capacidad Tanque',
            ))
            ->add('pesoTara', 'integer', array(
                'required' => true,
                'label' => 'Peso Tara',
            ))
            ->add('pesoBruto', 'integer', array(
                'required' => true,
                'label' => 'Peso Bruto',
            ))
            ->add('numeroPlazas', 'integer', array(
                'required' => true,
                'label' => 'Número Plazas',
            ))
            ->add('numeroCilindros', 'integer', array(
                'required' => true,
                'label' => 'Número Cilindros',
            ))
            ->add('cilindrada', 'integer', array(
                'required' => true,
                'label' => 'Cilindrada',
            ))
            ->add('potencia', 'integer', array(
                'required' => true,
                'label' => 'Potencia',
            ))
            ->add('validoHasta', 'date', array(
                'widget' => 'single_text',
                'format'  => 'dd/MM/yyyy',
                'label' => 'Válido Hasta',
            ))
            ->add('fechaRtv1', 'choice', array(
                'placeholder' => '---Seleccione---',
                'choices' => array(
                    'Enero'=>'Enero',
                    'Febrero' => 'Febrero',
                    'Marzo' => 'Marzo',
                    'Abril' => 'Abril',
                    'Mayo' => 'Mayo',
                    'Junio' => 'Junio',
                    'Julio' => 'Julio',
                    'Agosto' => 'Agosto',
                    'Septiembre' => 'Septiembre',
                    'Octubre' => 'Octubre',
                    'Noviembre' => 'Noviembre',
                    'Diciembre' => 'Diciembre',
                ),
            ))
            ->add('fechaRtv2', 'choice', array(
                'placeholder' => '---Seleccione---',
                'choices' => array(
                    'Enero'=>'Enero',
                    'Febrero' => 'Febrero',
                    'Marzo' => 'Marzo',
                    'Abril' => 'Abril',
                    'Mayo' => 'Mayo',
                    'Junio' => 'Junio',
                    'Julio' => 'Julio',
                    'Agosto' => 'Agosto',
                    'Septiembre' => 'Septiembre',
                    'Octubre' => 'Octubre',
                    'Noviembre' => 'Noviembre',
                    'Diciembre' => 'Diciembre',
                ),
            ))
            ->add('fechaIngreso', 'date', array(
                'widget' => 'single_text',
                'label' => 'Fecha Ingreso',
                'format'  => 'dd/MM/yyyy',
                'attr'   => array(
                    'class' => 'date',
                ),
            ))
            ->add('estilo','entity',array(
                'class' => 'BusetaNomencladorBundle:Estilo',
                'placeholder' => '---Seleccione---',
            ))

            ->add('color', 'entity', array(
                'class' => 'BusetaNomencladorBundle:Color',
                'placeholder' => '---Seleccione---',
            ))


            ->add('marcaMotor', 'entity', array(
                    'class' => 'BusetaNomencladorBundle:MarcaMotor',
                    'label' => 'Marca Motor',
                    'placeholder' => '---Seleccione---',
                ))
            ->add('combustible', 'entity', array(
                'class' => 'BusetaNomencladorBundle:Combustible',
                'placeholder' => '---Seleccione---',
            ))
            ->add('rampas', 'textarea', array(
                'required' => false,
                'label' => 'Rampas',
            ))
            ->add('barras', 'textarea', array(
                'required' => false,
                'label' => 'Barras',
            ))
            ->add('camaras', 'textarea', array(
                'required' => false,
                'label' => 'Cámaras',
            ))
            ->add('lectorCedulas', 'textarea', array(
                'required' => false,
                'label' => 'Lector cédulas',
            ))
            ->add('publicidad', 'textarea', array(
                'required' => false,
                'label' => 'Publicidad',
            ))
            ->add('gps', 'textarea', array(
                'required' => false,
                'label' => 'GPS',
            ))
            ->add('wifi', 'textarea', array(
                'required' => false,
                'label' => 'Wi-Fi',
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\BusesBundle\Form\Model\AutobusBasicoModel',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buses_autobus_basico';
    }
}
