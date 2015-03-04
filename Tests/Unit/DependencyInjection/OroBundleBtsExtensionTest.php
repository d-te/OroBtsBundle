<?php

namespace Oro\Bundle\BtsBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\BtsBundle\DependencyInjection\OroBundleBtsExtension;

class OroBundleBtsExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $extension = new OroBundleBtsExtension();
        $configs   = array();
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $extension->load($configs, $container);
    }
}
