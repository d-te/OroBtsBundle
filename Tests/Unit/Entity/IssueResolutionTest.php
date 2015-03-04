<?php

namespace Oro\Bundle\BtsBundle\Tests\Unit\Entity;

use Oro\Bundle\BtsBundle\Entity\IssueResolution;

class IssueResolutionTest extends \PHPUnit_Framework_TestCase
{

    public function testLabelSetterGetter()
    {
        $entity = new IssueResolution();
        $this->assertNull($entity->getLabel());
        $entity->setLabel('Label');
        $this->assertEquals('Label', $entity->getLabel());
    }

    public function testNameSetterGetter()
    {
        $entity = new IssueResolution();
        $this->assertNull($entity->getName());
        $entity->setName('Name');
        $this->assertEquals('Name', $entity->getName());
    }

    public function testLocaleSetterGetter()
    {
        $entity = new IssueResolution();
        $this->assertNull($entity->getLocale());
        $entity->setLocale('us');
        $this->assertEquals('us', $entity->getLocale());
    }

    public function testOrderSetterGetter()
    {
        $entity = new IssueResolution();
        $this->assertNull($entity->getOrder());
        $entity->setOrder(10);
        $this->assertEquals(10, $entity->getOrder());
    }
}
