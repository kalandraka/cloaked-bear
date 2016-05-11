<?php
namespace Buseta\BodegaBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProveedorFilter extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.codigo',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('alias', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.alias',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('nombres', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.nombres',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('apellidos', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.apellidos',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('moneda', 'entity', array(
                'class' => 'BusetaNomencladorBundle:Moneda',
                'placeholder' => '---Seleccione---',
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.moneda',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('cif_nif', 'text', array(
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.cifnif',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\BodegaBundle\Form\Model\ProveedorFilterModel',
            'method' => 'GET',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_proveedor_filter';
    }
}
