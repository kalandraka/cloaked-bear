<?php

namespace HatueySoft\MenuBundle\Menu;


use HatueySoft\MenuBundle\Model\MenuNode;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class MenuBuilder extends ContainerAware
{
    public function defaultModulesMenu(FactoryInterface $factory, array $options)
    {
        $builder = $this->container->get('hatuey_soft.menu_builder.service');
        $authorizationChecker = $this->container->get('security.authorization_checker');

        //Obteniendo el menu
        $modules = $builder->getModules();

        $menu = $factory->createItem($modules->getId());
        $menu->setChildrenAttribute('class', 'row-fluid');

        if (!$modules->getChildrens()->isEmpty()) {
            $counter = 0;
            foreach ($modules->getChildrens() as $children) {
                // check roles permission
                if (count($children->getRoles()) && $authorizationChecker->isGranted($children->getRoles()) && !$children->isDisabled()) {
                    $attributes = array(
                        'class' => 'span4',
                    );
                    if ($counter % 3 == 0) {
                        $attributes['style'] = 'margin-left:0;';
                    }
                    /** @var \HatueySoft\MenuBundle\Model\MenuNode $children */
                    $menu->addChild($children->getId(), array(
                        'label' => $children->getLabel(),
                        'route' => $children->getRoute(),
                        'attributes' => $attributes,
                    ));
                    $attributes = $children->getAttributesToArray();
                    $color = isset($attributes['color']) ? $attributes['color'] : 'bg-color-blue';
                    $icon  = isset($attributes['icon']) ? $attributes['icon'] : '';
                    $menu[$children->getId()]->setLinkAttribute('class', "tile wide $color imagetext  middle");
                    $menu[$children->getId()]->setExtra('icon', $icon);
                    $counter++;
                }
            }
        }

        return $menu;
    }

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $builder = $this->container->get('hatuey_soft.menu_builder.service');
        $authorizationChecker = $this->container->get('security.authorization_checker');

        //Obteniendo el menu
        $item = $builder->getModuleMenu($this->getName());

        $menu = $factory->createItem($item->getId());
        $menu->setChildrenAttribute('class', 'nav');
        $menu->setChildrenAttribute('id', 'side-menu');

        /** add principal page into all menus */
        $menu->addChild('pagina_principal', array(
            'label' => sprintf('<i class="fa fa-dashboard"></i> %s <span class="fa fa-bars pull-right" id="menu-toggle"></span>', 'Página principal'),
            'route' => 'core_homepage',
        ));
        $menu['pagina_principal']->setExtra('safe_label', true);

        if (!$item->getChildrens()->isEmpty()) {
            $this->renderChildrens($menu, $item, $authorizationChecker);
        }

        return $menu;
    }

    private function renderChildrens(ItemInterface $menu, MenuNode $item, AuthorizationCheckerInterface $authorizationChecker)
    {
        foreach ($item->getChildrens() as $children) {
            // check roles permission
            if (count($children->getRoles()) && $authorizationChecker->isGranted($children->getRoles()) && !$children->isDisabled()) {
                /** @var \HatueySoft\MenuBundle\Model\MenuNode $children */
                $options = array();
                if ($children->getRoute() !== null) {
                    $options['route'] = $children->getRoute();
                } else {
                    $options['uri'] = '#';
                }

                $menu->addChild($children->getId(), $options);
                $this->setLabel($menu[$children->getId()], $children);

                if (!$children->getChildrens()->isEmpty()) {
                    $menu[$children->getId()]->setChildrenAttribute('class', 'nav nav-second-level');
                    $this->renderChildrens($menu[$children->getId()], $children, $authorizationChecker);
                }
            }
        }
    }

    private function setLabel(ItemInterface $menu, MenuNode $item)
    {
        $attributes = $item->getAttributesToArray();
        $label = $item->getLabel();
        $icon = isset($attributes['icon']) ? $attributes['icon'] : null;
        if($icon !== null) {
            $label = sprintf('<i class="%s"></i> %s', $icon, $label);
            $menu->setExtra('safe_label', true);
        }

        if (!$item->getChildrens()->isEmpty()) {
            $label = sprintf('%s%s', $label, '<span class="fa arrow"></span>');
        }

        $menu->setLabel($label);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }
}
