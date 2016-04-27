<?php

namespace HatueySoft\DateTimeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FechaSistemaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activo', 'checkbox', array(
                'required' => false,
            ))
            ->add('fecha','date',array(
                    'required' => false,
                    'label' => 'Fecha de Sistema',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'attr' => array(
                        'class' => 'pickadate-fecha'
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
            'data_class' => 'HatueySoft\DateTimeBundle\Entity\FechaSistema'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'hatueysoft_datetime_fechasistema_type';
    }
}
