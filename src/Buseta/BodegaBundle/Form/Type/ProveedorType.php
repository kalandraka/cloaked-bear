<?php

namespace Buseta\BodegaBundle\Form\Type;

use Buseta\NomencladorBundle\Form\MarcaProveedorType;
use HatueySoft\UploadBundle\Form\Type\UploadResourcesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProveedorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('proveedorId', 'integer', array(
                'required' => false,
                'label' => false,
                'attr' => array(
                    'class' => 'hidden',
                ),
            ))
            ->add('foto', new UploadResourcesType(), array(
                'required' => false,
                'label' => false,
                'attr' => array(
                    'class' => 'hidden',
                ),
            ))
            ->add('alias', 'text', array(
                'required' => true,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.alias',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('nombre', 'text', array(
                'required' => true,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.nombres',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('telefono', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.telefono',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('fax', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.fax',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('web', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.web',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('direccion', 'textarea', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.direccion',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('ciudad', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.ciudad',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('region', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.region',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('apartado', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.apartado',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('pais', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.pais',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('contacto', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.contacto',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('puesto', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.puesto',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('celular', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.celular',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('email', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.email',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('moneda', 'entity', array(
                'required' => true,
                'class' => 'BusetaNomencladorBundle:Moneda',
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.moneda',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('creditoLimite', 'number', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.credito',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('cif_nif', 'text', array(
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.cifnif',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('observaciones', 'textarea', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.observaciones',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('pago', 'text', array(
                'required' => false,
                'translation_domain' => 'BusetaBodegaBundle',
                'label' => 'proveedor.pago',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('marcas', 'collection', array(
                'type' => new MarcaProveedorType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\BodegaBundle\Form\Model\ProveedorModel',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buseta_bodegabundle_proveedor';
    }
}
