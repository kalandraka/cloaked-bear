<?php
namespace Buseta\TallerBundle\Form\Filter;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrdenTrabajoFilter extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', 'text', array(
                'required' => false,
                'label'  => 'Número',
                'attr'   => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('requisionMateriales', 'text', array(
                'required' => false,
                'label' => 'Número control materiales',
                'attr'   => array(
                    'class' => 'form-control',
                ),
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
                        ->andWhere($qb->expr()->isNotNull('usuario'));

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
                        ->andWhere($qb->expr()->isNotNull('persona'));

                    return $qb;
                },
            ))
            ->add('autobus', 'entity', array(
                'class' => 'BusetaBusesBundle:Autobus',
                'empty_value' => '---Seleccione---',
                'label' => 'Autobús',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('diagnostico','entity',array(
                'class' => 'BusetaTallerBundle:Diagnostico',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('d');
                    return $qb->leftJoin('d.ordenTrabajo', 'ot')
                        ->where($qb->expr()->isNull('ot'));
                },
                'empty_value' => '---Seleccione---',
                'label' => 'Diagnóstico',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))

        ;

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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