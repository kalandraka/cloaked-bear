<?php

namespace Buseta\TallerBundle\Form\Type;

use Buseta\TallerBundle\Form\Model\OrdenTrabajoModel;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Buseta\TallerBundle\Form\EventListener\AddAutobusFieldSubscriber;
use Buseta\TallerBundle\Form\EventListener\AddDiagnosticadoporFieldSubscriber;
use Buseta\TallerBundle\Form\EventListener\AddKilometrajeFieldSubscriber;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Util\ClassUtils;
use Symfony\Component\Validator\Constraints\NotBlank;


class OrdenTrabajoType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Container
     */
    private $serviceContainer;

    /**
     * Constructor
     *
     * @param TokenStorageInterface|SecurityContextInterface $tokenStorage
     * @param Container                                      $serviceContainer
     */
    function __construct(TokenStorageInterface $tokenStorage, Container $serviceContainer)
    {
        $this->tokenStorage = $tokenStorage;
        $this->serviceContainer = $serviceContainer;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'preSetData'));
        $attr['readonly'] = true;
        $builder
            ->add('id', 'hidden',  array(
                'required' => false,
                'virtual' => true,
            ))
            ->add('requisionMateriales', 'text', array(
                    'required' => false,
                    'label' => 'Número control materiales',
                    'attr'   => array(
                        'class' => 'form-control',
                    ),
                ))
            ->add('realizadaPor', 'entity', array(
                'class' => 'BusetaBodegaBundle:Tercero',
                'required' => false,
                'label'  => 'Responsable',
                'attr'   => array(
                    'class' => 'form-control',
                ),
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('responsable');
                    $qb->join('responsable.persona', 'persona')
                        ->andWhere($qb->expr()->isNotNull('persona.id'));

                    return $qb;
                },
            ))


            ->add('autobus', 'entity', array(
                'class' => 'BusetaBusesBundle:Autobus',
                'required' => false,
                'label'  => 'Autobus',
                'attr'   => array(
                    'class' => 'form-control',
                ),
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('o');

                    $user = $this->tokenStorage->getToken()->getUser();
                    if (ClassUtils::getRealClass($user) === 'HatueySoft\SecurityBundle\Entity\User'){
                        $grupo = $user->getGrupoBuses();

                        $grupoBuses = array();

                        foreach ($grupo as $grupos) {
                            $grupoBuses[] = $grupos->getId();
                        }

                        if (count($grupoBuses)>0){
                            $qb->andWhere(sprintf('o.grupobuses IN (%s)', implode(',', $grupoBuses)));
                        };
                    }

                    return $qb;
                },
            ))

            ->add('diagnosticadoPor', 'entity', array(
                'class' => 'BusetaBodegaBundle:Tercero',
                'required' => false,

                'label'  => 'Diagnosticado por',
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
            ->add('prioridad', 'choice', array(
                'label'  => 'Prioridad',
                'choices' => array(
                    'rapida' => 'Rápida',
                    'normal' => 'Normal',
                ),
                'data' => 'normal',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('ayudante', 'entity', array(
                'class' => 'BusetaBodegaBundle:Tercero',
                'required' => false,
                'label'  => 'Ayudante',
                'attr'   => array(
                    'class' => 'form-control',
                ),
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('ayudante');
                    $qb->join('ayudante.persona', 'persona')
                        ->andWhere($qb->expr()->isNotNull('persona.id'));

                    return $qb;
                },
            ))
            //Revisar la regla de negocio esta
            ->add('diagnostico','entity',array(
                'class' => 'BusetaTallerBundle:Diagnostico',
                'query_builder' => function (EntityRepository $er)   {
                     $qb = $er->createQueryBuilder('d');
                     $qb->leftJoin('d.ordenTrabajo','ot')
                        ->where($qb->expr()->isNull('ot.id'))
                        ->andWhere($qb->expr()->eq('d.estado', ':estado'));
                     $qb->setParameter('estado','BO');
                    return $qb;
                },
                'placeholder' => '---Seleccione---',
                'label' => 'Diagnóstico',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('tareasAdicionales', 'collection', array(
                'type' => new TareaAdicionalType(),
                'label'  => false,
                'required' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('kilometraje', 'number', array(
                'required' => false,
                'label' => 'Kilometraje',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('fechaInicio', 'date', array(
                'required' => false,
                'label'  => 'Fecha inicio',
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('fechaFinal', 'date', array(
                'required' => false,
                'label'  => 'Fecha final',
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('duracionDias', 'text', array(
                'required' => false,
                'label'  => 'Duración de días',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('duracionHorasLaboradas', 'text', array(
                'required' => false,
                'label'  => 'Duración de horas laboradas',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('aprobadoPor', 'entity', array(
                'class' => 'BusetaBodegaBundle:Tercero',
                'required' => false,
                'label'  => 'Aprobado por',
                'attr'   => array(
                    'class' => 'form-control',
                ),
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('tercero');
                    return $qb->join('tercero.persona', 'persona')
                        ->andWhere($qb->expr()->isNotNull('persona.id'));
                },
            ))
            ->add('revisadoPor', 'entity', array(
                'class' => 'BusetaBodegaBundle:Tercero',
                'required' => false,
                'label'  => 'Revisado por',
                'attr'   => array(
                    'class' => 'form-control',
                ),
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('tercero');
                    $qb->join('tercero.persona', 'persona')
                        ->andWhere($qb->expr()->isNotNull('persona.id'));

                    return $qb;
                },
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\TallerBundle\Form\Model\OrdenTrabajoModel',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_tallerbundle_ordentrabajo';
    }

    public function preSetData(FormEvent $formEvent)
    {
        $data = $formEvent->getData();
        $form = $formEvent->getForm();
        $sequenceManager = $this->serviceContainer->get('hatuey_soft.sequence.manager');

        if ($data instanceof OrdenTrabajoModel && !$data->getNumero()
            && !$sequenceManager->hasSequence('Buseta\TallerBundle\Entity\OrdenTrabajo')) {
            $form->add('numero', 'text', array(
                'required' => true,
                'label'  => 'Número',
                'constraints' => array(
                    new NotBlank(),
                )
            ));
        } elseif ($data instanceof OrdenTrabajoModel && $data->getNumero()) {
            $form->add('numero', 'text', array(
                'required' => true,
                'read_only' => true,
                'data' => $data->getNumero(),
                'label'  => 'Número',
            ));
        }

    }


}
