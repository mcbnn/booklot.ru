<?php

namespace Application\Model;

use Zend\Navigation\Service\DefaultNavigationFactory;

class SecondaryNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'admin_menu';
    }
}


?>