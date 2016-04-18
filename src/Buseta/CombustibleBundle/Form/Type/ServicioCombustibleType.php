<?php

namespace Buseta\CombustibleBundle\Form\Type;

use Buseta\CombustibleBundle\Form\Type\ChoferInServicioCombustibleType;
use HatueySoft\DateTimeBundle\Managers\FechaSistemaManager;
use HatueySoft\DateTimeBundle\Twig\FechaSistemaExtension;
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
    private $fechaSistemaManager;


    public function __construct(ObjectManager $em, Container $serviceContainer, FechaSistemaManager $fechaSistemaManager)
    {
        $this->em = $em;
        $this->serviceContainer = $serviceContainer;
        $this->fechaSistemaManager = $fechaSistemaManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chofer', new ChoferInServicioCombustibleType())
            ->add('combustible','entity',array(
                'class' => 'BusetaCombustibleBundle:ConfiguracionCombustible',
                'label' => 'Nomenclador de Combustible',
                'required' => true,
            ))
            ->add('odometro', 'integer', array(
                'required' => true,
                'label' => 'Odometro',
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
            $form = $event->getForm();

            $fechaSistema = $this->fechaSistemaManager->getFechaSistema();

            $fechaInicial = new \DateTime(date_format($fechaSistema, 'Y-m-d H:i:s'));
            $fechaInicial->modify('-1 day');

            $form->add('vehiculo', 'entity', array(
                'class' => 'BusetaBusesBundle:Vehiculo',
                'query_builder' => function(EntityRepository $repository) use ($fechaInicial, $fechaSistema) {
                    $qb = $repository->createQueryBuilder('vehiculo');
                    $qb
                        ->where('NOT EXISTS (
                            SELECT ln
                            FROM BusetaCombustibleBundle:ListaNegraCombustible ln
                            INNER JOIN ln.autobus a
                            WHERE a=vehiculo
                                AND ln.fechaInicio <= :fechaActual
                                AND ln.fechaFinal >= :fechaActual
                        )')
                        ->andWhere('NOT EXISTS (
                            SELECT s
                            FROM BusetaCombustibleBundle:ServicioCombustible s
                            INNER JOIN s.vehiculo v
                            WHERE v=vehiculo
                                AND s.fecha >= :fechaInicial
                                AND s.fecha <= :fechaActual
                        )')
                        ->orderBy('vehiculo.matricula')
                        ->setParameter('fechaActual', $fechaSistema)
                        ->setParameter('fechaInicial', $fechaInicial)
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
        return 'combustible_servicio_combustible_type';
    }

    /**
     * @param FormEvent $event
     */
    public function boletaPreSetData(FormEvent $event)
    {
        $fechaSistema = $this->fechaSistemaManager->getFechaSistema();
        $resource = curl_init();
        $serverApi = $this->serviceContainer->getParameter('buseta_combustible.server');
        $url = sprintf('http://%s/boleta/api/boletas', $serverApi['address']);
        curl_setopt_array($resource, array(
            CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query(array(
                    'fecha' => date_format($fechaSistema, 'Y-m-d')
                )),
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 4
        ));

        $result = curl_exec($resource);
        $boletasArray = json_decode($result);
        curl_close($resource);

        $choices = array();
        if ($boletasArray !== null && is_array($boletasArray)) {
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
