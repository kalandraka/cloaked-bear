<?php

namespace Buseta\EmpleadosBundle\Menu;

use HatueySoft\MenuBundle\Menu\MenuBuilder as BaseMenuBuilder;

class MenuBuilder extends BaseMenuBuilder
{
    public function getName()
    {
        return 'menu_empleados';
    }
}
