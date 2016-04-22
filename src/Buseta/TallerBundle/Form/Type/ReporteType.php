<?php

namespace Buseta\TallerBundle\Form\Type;

use Buseta\TallerBundle\Form\Model\ReporteModel;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Symfony\Component\DependencyInjection\Dump\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReporteType extends AbstractType
{

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var SequenceManager
     */
    private $sequenceManager;

    public function __construct(ObjectManager $em, SequenceManager $sequenceManager)
    {
        $this->em = $em;
        $this->sequenceManager = $sequenceManager;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'preSetData'));
        $builder
            ->add('autobus','entity',array(
                'class' => 'BusetaBusesBundle:Autobus',
                'placeholder' => '---Seleccione autobus---',
                'required' => true,
            ))
            ->add('prioridad', 'entity', array(
                'class' => 'BusetaNomencladorBundle:PrioridadSolicitud',
                'placeholder' => '---Seleccione prioridad---',
                'required' => false,
            ))
            ->add('grupo', 'entity', array(
                'class' => 'BusetaNomencladorBundle:Grupo',
                'placeholder' => '---Seleccione grupo---',
                'required' => false,
            ))
            ->add('observaciones','collection',array(
                'type' => new ObservacionType(),
                'label'  => false,
                'required' => true,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('medioReporte','entity',array(
                'class' => 'BusetaNomencladorBundle:MedioReporte',
                'placeholder' => '---Seleccione medio reporte---',
                'required' => true,
            ))
            ->add('reporta', 'entity', array(
                'class' => 'BusetaBodegaBundle:Tercero',
                'required' => false,
                'label'  => 'Reporta',
                'query_builder' => function(EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('reporta');
                    $qb->innerJoin('reporta.persona', 'p');

                    return $qb;
                }
            ))
            ->add('esUsuario', null, array(
                'required' => false,
            ))
            ->add('nombrePersona', 'text', array(
                'required' => false,
                'label'  => 'Nombre Persona',
            ))
            ->add('emailPersona', 'text', array(
                'required' => false,
                'label'  => 'Email Persona',
            ))
            ->add('telefonoPersona', 'text', array(
                'required' => false,
                'label'  => 'Teléfono Persona',
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\TallerBundle\Form\Model\ReporteModel'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_tallerbundle_reporte';
    }

    public function preSetData(FormEvent $formEvent)
    {
        $data = $formEvent->getData();
        $form = $formEvent->getForm();
        $sequenceManager = $this->sequenceManager;

        if ($data instanceof ReporteModel && !$data->getNumero()
            && !$sequenceManager->hasSequence('Buseta\TallerBundle\Entity\Reporte')) {
            $form->add('numero', 'text', array(
                'required' => true,
                'label'  => 'Número',
                'constraints' => array(
                    new NotBlank(),
                )
            ));
        } elseif ($data instanceof ReporteModel && $data->getNumero()) {
            $form->add('numero', 'text', array(
                'required' => true,
                'read_only' => true,
                'data' => $data->getNumero(),
                'label'  => 'Número',
            ));
        }

    }
}
