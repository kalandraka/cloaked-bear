<?php

namespace Buseta\TallerBundle\Form\Type;

use Buseta\TallerBundle\Form\EventListener\AddSolicitudFieldSubscriber;
use Buseta\TallerBundle\Form\Model\DiagnosticoModel;
use Buseta\TallerBundle\Form\Type\TareaDiagnosticoType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DiagnosticoType extends AbstractType
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
        $builder->addEventSubscriber(new AddSolicitudFieldSubscriber());
        $attr['readonly'] = true;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'preSetData'));
        $builder
            ->add('autobus','entity',array(
                'class' => 'BusetaBusesBundle:Autobus',
                'placeholder' => '---Seleccione---',
                'label' => 'Autobús',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('prioridad', 'entity', array(
                'class' => 'BusetaNomencladorBundle:PrioridadSolicitud',
                'placeholder' => '---Seleccione prioridad---',
                'required' => false,
            ))
            ->add('tareaDiagnostico', 'collection', array(
                'type' => new TareaDiagnosticoType(),
                'label'  => false,
                'required' => true,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('observaciones','collection',array(
                'type' => new ObservacionDiagnosticoType(),
                'label'  => false,
                'required' => true,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\TallerBundle\form\Model\DiagnosticoModel'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_tallerbundle_diagnostico';
    }

    public function preSetData(FormEvent $formEvent)
    {
        $data = $formEvent->getData();
        $form = $formEvent->getForm();
        $sequenceManager = $this->sequenceManager;

        if ($data instanceof DiagnosticoModel && !$data->getNumero()
            && !$sequenceManager->hasSequence('Buseta\TallerBundle\Entity\Diagnostico')) {
            $form->add('numero', 'text', array(
                'required' => true,
                'label'  => 'Número',
                'constraints' => array(
                    new NotBlank(),
                )
            ));
        } elseif ($data instanceof DiagnosticoModel && $data->getNumero()) {
            $form->add('numero', 'text', array(
                'required' => true,
                'read_only' => true,
                'data' => $data->getNumero(),
                'label'  => 'Número',
            ));
        }

    }
}
