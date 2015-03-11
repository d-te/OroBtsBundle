<?php

namespace Oro\Bundle\BtsBundle\Tests\Unit\Model;

use Oro\Bundle\BtsBundle\Entity\Issue as Entity;
use Oro\Bundle\BtsBundle\Entity\IssueType;
use Oro\Bundle\BtsBundle\Model\Issue;
use Oro\Bundle\BtsBundle\Entity\IssueWorkflowStep;

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

    public function testIsStory()
    {
        $issue = new Entity();

        $model = new Issue($issue);

        $this->assertFalse($model->isStory());

        $issueType = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\IssueType')
            ->setMethods(array('getName'))
            ->getMock();
        $issueType
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(IssueType::STORY));

        $issue = new Entity();
        $issue->setType($issueType);

        $model = new Issue($issue);
        $this->assertTrue($model->isStory());
    }

    public function testIsSubtask()
    {
        $issue = new Entity();

        $model = new Issue($issue);

        $this->assertFalse($model->isSubtask());

        $issueType = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\IssueType')
            ->setMethods(array('getName'))
            ->getMock();
        $issueType
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(IssueType::SUBTASK));

        $issue = new Entity();
        $issue->setType($issueType);

        $model = new Issue($issue);
        $this->assertTrue($model->isSubtask());
    }

    public function testIsDeletableIssueNotClosed()
    {
        $step = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\IssueWorkflowStep')
            ->setMethods(array('getName'))
            ->getMock();
        $step
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(IssueWorkflowStep::OPENED));

        $issue = new Entity();
        $issue->setWorkflowStep($step);

        $model = new Issue($issue);
        $this->assertFalse($model->isDeletable());
    }

    public function testIsDeletableIssueClosed()
    {
        $step = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\IssueWorkflowStep')
            ->setMethods(array('getName'))
            ->getMock();
        $step
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(IssueWorkflowStep::CLOSED));

        $issue = new Entity();
        $issue->setWorkflowStep($step);

        $model = new Issue($issue);
        $this->assertTrue($model->isDeletable());
    }

    public function testIsDeletableChildrenNotClosed()
    {
        $step = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\IssueWorkflowStep')
            ->setMethods(array('getName'))
            ->getMock();
        $step
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(IssueWorkflowStep::CLOSED));

        $subtaskStep = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\IssueWorkflowStep')
            ->setMethods(array('getName'))
            ->getMock();
        $subtaskStep
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(IssueWorkflowStep::OPENED));

        $subtask = new Entity();
        $subtask->setWorkflowStep($subtaskStep);

        $issue = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\Issue')
            ->setMethods(array('getChildren', 'getWorkFlowStep'))
            ->getMock();
        $issue
            ->expects($this->once())
            ->method('getWorkFlowStep')
            ->will($this->returnValue($step));
        $issue
            ->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue([$subtask]));

        $model =  $this->getMockBuilder('Oro\Bundle\BtsBundle\Model\Issue')
            ->setConstructorArgs(array($issue))
            ->setMethods(array('isStory'))
            ->getMock();
        $model
            ->expects($this->once())
            ->method('isStory')
            ->will($this->returnValue(true));

        $this->assertFalse($model->isDeletable());
    }
}
