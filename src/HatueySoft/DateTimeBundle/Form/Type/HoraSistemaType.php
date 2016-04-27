<?php

namespace HatueySoft\DateTimeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HoraSistemaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hora','time',array(
                    'required' => false,
                    'label' => 'Hora de Cambio',
                    'widget' => 'single_text',
                    'attr' => array(
                        'class' => 'pickatime'
                    )
                ))
            ->add('activo', 'checkbox', array(
                'required' => false,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HatueySoft\DateTimeBundle\Entity\CambioHoraSistema'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'hatueysoft_datetime_horasistema_type';
    }
}
