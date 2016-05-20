<?php

namespace HatueySoft\DateTimeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            ));


        $formModifier = function (FormInterface $form, $active = null) {
            $formOptions = array(
                'required' => false,
                'label' => 'Fecha de Sistema',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            );

            if ($active !== null && $active) {
                $formOptions['constraints'] = array(
                    new NotBlank(),
                );
            }

            $form->add('fecha', 'date', $formOptions);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            $form = $event->getForm();
            $data = $event->getData();

            $accessor = PropertyAccess::createPropertyAccessor();

            if ($data !== null && $accessor->getValue($data, 'activo')) {
                $formModifier($form, $accessor->getValue($data, 'activo'));
            } else {
                $formModifier($form);
            }
        });

        $builder->get('activo')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $activo = $event->getForm()->getData();

                if ($activo) {
                    $formModifier($event->getForm()->getParent(), true);
                } else {
                    $formModifier($event->getForm()->getParent());
                }
            }
        );
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
        return 'datetime_fechasistema_type';
    }
}
