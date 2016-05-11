<?php
namespace Buseta\TallerBundle\Form\Filter;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReporteFilter extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', null, array(
                'required'  => false,
                'label' => 'Número',
                'trim'      => true,
                'attr'      => array(
                    'class' => 'form-control',
                )
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
            ->add('fechaInicio', 'date', array(
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ))
            ->add('fechaFin', 'date', array(
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ))
        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\TallerBundle\Form\Model\ReporteFilterModel',
            'method' => 'GET',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_reporte_filter';
    }
}
