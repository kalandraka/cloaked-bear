<?php

namespace Buseta\NotificacionesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigNotificacionesType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $usuariosChoices = array();
        $usuariosSistema = $this->em->getRepository('HatueySoftSecurityBundle:User')->findBy(
            array(
                'enabled' => true,
            ),
            array(
                'username' => 'ASC',
            )
        );

        foreach($usuariosSistema as $usuario){
            /* @var \HatueySoft\SecurityBundle\Entity\User $usuario */
            $usuariosChoices[$usuario->getUsername()] = $usuario->getNombreCompleto().'('.$usuario->getUsername().')';
        }

        $builder
            ->add('codigo','hidden',array())
            ->add('asunto','text',array(
                'label' => 'Enviar con Asunto',
                'required' => false,
            ))
            ->add('correoSelector','email',array(
                'label'  => 'Correos',
                'mapped' => false,
                'required' => false,
            ))
            ->add('correosDefinidos','collection',array(
                'type' => 'hidden',
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
            ))
            ->add('usuariosDefinidos','choice',array(
                'label'    => 'Usuarios',
                'choices'  => $usuariosChoices,
                'multiple' => true,
            ))
            ->add('notificacionInterna','checkbox',array(
                'label' => 'Notificar internamente',
                'required' => false,
            ))
            ->add('notificacionCorreo','checkbox',array(
                'label' => 'Notificar por correo',
                'required' => false,
            ))
            ->add('activo','checkbox',array(
                'label' => 'Activo',
                'required' => false,
            ))
            /*->add('horariosSelector','text',array(
                'label'  => 'Horarios',
                'mapped' => false,
            ))
            ->add('horarios','collection',array(
                'type' => 'hidden',
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
            ))
            ->add('frecuencia','number',array(
                'label' => 'Frecuencia',
            ))*/;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Buseta\NotificacionesBundle\Form\Models\ConfigNotificacionesCustom'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'notificaciones_config_type';
    }
}
