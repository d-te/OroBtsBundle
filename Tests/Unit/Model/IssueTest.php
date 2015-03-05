<?php

namespace Oro\Bundle\BtsBundle\Tests\Unit\Model;

use Oro\Bundle\BtsBundle\Model\Issue;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCode()
    {
        $organization = $this->getMockBuilder('Oro\Bundle\OrganizationBundle\Entity\Organization')
            ->setMethods(array('getName'))
            ->getMock();
        $organization
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('ORO'));

        $issue = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\Issue')
            ->setMethods(array('getId'))
            ->getMock();
        $issue
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(5));

        $issue->setOrganization($organization);

        $model = new Issue($issue);

        $this->assertEquals('ORO - 5', $model->getCode());
    }
}
