<?php
namespace Buseta\TallerBundle\Form\Filter;

use Buseta\BodegaBundle\Form\EventListener\AddSubgrupoFieldSubscriber;
use Buseta\TallerBundle\Form\EventListener\AddGrupoFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TareaMantenimientoFilter extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $objeto = $builder->getFormFactory();
        $subgrupoSubscriber = new AddSubgrupoFieldSubscriber($objeto);
        $builder->addEventSubscriber($subgrupoSubscriber);
        $grupoSubscriber = new AddGrupoFieldSubscriber($objeto);
        $builder->addEventSubscriber($grupoSubscriber);

        $builder
            ->add('valor','entity',array(
                'class' => 'BusetaNomencladorBundle:Tarea',
                'placeholder' => '---Seleccione---',
                'label' => 'Valor',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('garantia','entity',array(
                'class' => 'BusetaNomencladorBundle:GarantiaTarea',
                'placeholder' => '---Seleccione---',
                'label' => 'Garantía',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))

        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\TallerBundle\Form\Model\TareaMantenimientoFilterModel',
            'method' => 'GET',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_tareamantenimiento_filter';
    }
}
