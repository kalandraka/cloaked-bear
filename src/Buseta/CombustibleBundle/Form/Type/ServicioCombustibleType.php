<?php

namespace Buseta\CombustibleBundle\Form\Type;

use Buseta\CombustibleBundle\Form\Type\ChoferInServicioCombustibleType;
use Buseta\CoreBundle\Managers\CambioHoraSistemaManager;
use Buseta\CoreBundle\Managers\FechaSistemaManager;
use Buseta\CoreBundle\Twig\FechaSistemaExtension;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class ServicioCombustibleType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var Container
     */
    private $serviceContainer;

    /**
     * @var FechaSistemaManager
     */
    private $fechaSistema;

    /**
     * @var CambioHoraSistemaManager
     */
    private $horaSistema;

    public function __construct(ObjectManager $em, Container $serviceContainer, FechaSistemaManager $fechaSistema, CambioHoraSistemaManager $horaSistema)
    {
        $this->em = $em;
        $this->serviceContainer = $serviceContainer;
        $this->fechaSistema = $fechaSistema;
        $this->horaSistema = $horaSistema;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chofer', new ChoferInServicioCombustibleType())
            ->add('combustible','entity',array(
                'class' => 'BusetaCombustibleBundle:ConfiguracionCombustible',
                'placeholder' => '---Seleccione---',
                'label' => 'Nomenclador de Combustible',
                'required' => true,
            ))
            ->add('cantidadLibros', 'integer', array(
                'required' => true,
                'label' => 'Cantidad de Libros',
            ))
            ->add('marchamo1')
            ->add('marchamo2')
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'boletaPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            //$hora = $this->horaSistema->getHoraCambio()->format('H:i:s');
            $hora = explode(':', '00:00:00');

//            $fechaActualInicial = $this->fechaSistema->getFechaSistema();
            $fechaActualInicial = new \DateTime();
            $fechaActualInicial->setTime($hora[0], $hora[1], $hora[2]);

            $fechaActualFinal = new \DateTime();
            $fechaActualFinal->modify('+1 days');

            $fechaSistema = new \DateTime();

            $form->add('vehiculo', 'entity', array(
                'class' => 'BusetaBusesBundle:Vehiculo',
                'query_builder' => function(EntityRepository $repository) use ($fechaActualInicial,$fechaActualFinal,$fechaSistema) {
                    $qb = $repository->createQueryBuilder('vehiculo');
                    $qb
                        ->where('NOT EXISTS(SELECT ln FROM BusetaCombustibleBundle:ListaNegraCombustible ln INNER JOIN ln.autobus a WHERE a=vehiculo AND ln.fechaInicio<=:fechaActual AND ln.fechaFinal>=:fechaActual )')
                        ->andWhere('NOT EXISTS(SELECT s FROM BusetaCombustibleBundle:ServicioCombustible s INNER JOIN s.vehiculo v WHERE v=vehiculo AND s.created>:fechaActualInicial AND s.created<:fechaActualFinal)')
                        ->orderBy('vehiculo.matricula')
                        ->setParameter('fechaActual', $fechaSistema)
                        ->setParameter('fechaActualInicial', $fechaActualInicial)
                        ->setParameter('fechaActualFinal', $fechaActualFinal)
                    ;
                    return $qb;
                },
                'placeholder' => '---Seleccione---',
                'label' => 'Vehículo',
                'required' => false,
            ));

            /*$form->add('autobus', 'entity', array(
                'class' => 'BusetaBusesBundle:Autobus',
                'query_builder' => function(EntityRepository $repository) use ($fechaActualInicial,$fechaActualFinal) {
                    $qb = $repository->createQueryBuilder('bus');
                    $qb
                        ->where('NOT EXISTS(SELECT ln FROM BusetaCombustibleBundle:ListaNegraCombustible ln INNER JOIN ln.autobus a WHERE a=bus AND ln.fechaInicio<=:fechaActual AND ln.fechaFinal>=:fechaActual )')
                        ->andWhere('NOT EXISTS(SELECT d FROM BusetaCombustibleBundle:ServicioCombustible d INNER JOIN d.autobus au WHERE au=bus AND d.created>:fechaActualInicial AND d.created<:fechaActualFinal)')
                        ->orderBy('bus.matricula')
                        ->setParameter('fechaActual', new \DateTime())
                        ->setParameter('fechaActualInicial', $fechaActualInicial)
                        ->setParameter('fechaActualFinal', $fechaActualFinal)
                    ;
                    return $qb;
                },

                'placeholder' => '---Seleccione---',
                'label' => 'Autobús',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ));*/

        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\CombustibleBundle\Form\Model\ServicioCombustibleModel'
        ));
    }

    public function getName()
    {
        return 'combustible_servicio_combustible';
    }

    /**
     * @param FormEvent $event
     */
    public function boletaPreSetData(FormEvent $event)
    {
        $resource = curl_init();
        $serverApi = $this->serviceContainer->getParameter('buseta_combustible.server');
        $url = sprintf('http://%s/boleta/api/boletas', $serverApi['address']);
        curl_setopt_array($resource, array(
            CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query(array(
                    'fecha' => null
                )),
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 4
        ));

        $result = curl_exec($resource);
        $boletasArray = json_decode($result);
        curl_close($resource);

        $choices = array();
        if ($boletasArray !== null) {
            foreach ($boletasArray as $value) {
                $choices[$value->identificador] = $value->identificador;
            }
        }

        $data = $event->getData();
        $form = $event->getForm();

        $form->add('boleta', 'choice', array(
            'required' => false,
            'label' => 'Boleta',
            'choices' => $choices,
            'placeholder' => '---Seleccione---',
        ));
    }
}
