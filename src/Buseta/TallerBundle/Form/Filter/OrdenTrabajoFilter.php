<?php
namespace Buseta\TallerBundle\Form\Filter;

use Buseta\TallerBundle\Form\EventListener\AddAutobusFieldSubscriber;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdenTrabajoFilter extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //$builder->addEventSubscriber(new AddAutobusFieldSubscriber());

        $builder
            ->add('numero', 'text', array(
                'required' => false,
                'label'  => 'Número',
            ))
            ->add('requisionMateriales', 'text', array(
                'required' => false,
                'label' => 'Número control materiales',
            ))
            ->add('diagnosticadoPor', 'entity', array(
                'class' => 'BusetaBodegaBundle:Tercero',
                'required' => false,
                'label'  => 'Diagnosticado por',
                // Esta consulta hace que en el select solo aparezcan las entradas de la tabla terceros que tengan el campo
                // usuario no nulo, o sea muestra los terceros que tengan usuarios asignados
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('responsable');
                    $qb->join('responsable.usuario', 'usuario')
                        ->andWhere($qb->expr()->isNotNull('usuario.id'));

                    return $qb;
                },
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

            ->add('autobus', 'entity', array(
                'class' => 'BusetaBusesBundle:Autobus',
                'placeholder' => '---Seleccione---',
                'label' => 'Autobús',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->select('a, filtro_aceite, filtro_agua, filtro_caja, filtro_diesel, filtro_hidraulico, filtro_transmision')
                        ->leftJoin('a.filtroAceite', 'filtro_aceite')
                        ->leftJoin('a.filtroAgua', 'filtro_agua')
                        ->leftJoin('a.filtroCaja', 'filtro_caja')
                        ->leftJoin('a.filtroDiesel', 'filtro_diesel')
                        ->leftJoin('a.filtroHidraulico', 'filtro_hidraulico')
                        ->leftJoin('a.filtroTransmision', 'filtro_transmision')
                        ;
                }
            ))

            ->add('diagnostico','entity',array(
                'class' => 'BusetaTallerBundle:Diagnostico',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('d')
                        ->select('d, ot');
                    return $qb->leftJoin('d.ordenTrabajo', 'ot')
                        ->where($qb->expr()->isNull('ot.id'));
                },
                'placeholder' => '---Seleccione---',
                'label' => 'Diagnóstico',
                'required' => false,
            ))

        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\TallerBundle\Form\Model\OrdenTrabajoFilterModel',
            'method' => 'GET',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_ordentrabajo_filter';
    }
}
