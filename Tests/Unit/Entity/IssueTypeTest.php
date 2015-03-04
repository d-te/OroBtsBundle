<?php

namespace Oro\Bundle\BtsBundle\Tests\Unit\Entity;

use Oro\Bundle\BtsBundle\Entity\IssueType;

class IssueTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testLabelSetterGetter()
    {
        $entity = new IssueType();
        $this->assertNull($entity->getLabel());
        $entity->setLabel('Label');
        $this->assertEquals('Label', $entity->getLabel());
    }

    public function testNameSetterGetter()
    {
        $entity = new IssueType();
        $this->assertNull($entity->getName());
        $entity->setName('Name');
        $this->assertEquals('Name', $entity->getName());
    }

    public function testLocaleSetterGetter()
    {
        $entity = new IssueType();
        $this->assertNull($entity->getLocale());
        $entity->setLocale('us');
        $this->assertEquals('us', $entity->getLocale());
    }

    public function testOrderSetterGetter()
    {
        $entity = new IssueType();
        $this->assertNull($entity->getOrder());
        $entity->setOrder(10);
        $this->assertEquals(10, $entity->getOrder());
    }
}
