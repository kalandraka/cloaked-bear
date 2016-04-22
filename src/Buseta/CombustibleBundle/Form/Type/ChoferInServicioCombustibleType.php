<?php

namespace Buseta\CombustibleBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoferInServicioCombustibleType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chofer','entity',array(
                'class' => 'BusetaBusesBundle:Chofer',
                'placeholder' => '---Seleccione---',
                'required' => false,
            ))
            ->add('codigobarras','password',array(
                'required' => false,
                'label' => 'Código de Barras',
            ))
            ->add('pin', 'password', array(
                'required' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Buseta\CombustibleBundle\Form\Model\ChoferInServicioCombustible',
            ));
    }

    public function getName()
    {
        return 'combustible_choferinservicio_combustible_type';
    }
}
