<?php

namespace Oro\Bundle\BtsBundle\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\BtsBundle\Entity\Issue;
use Oro\Bundle\BtsBundle\Entity\IssueType;
use Oro\Bundle\BtsBundle\Entity\IssuePriority;
use Oro\Bundle\BtsBundle\Entity\IssueResolution;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    public function testSummarySetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getSummary());
        $entity->setSummary('Summary');
        $this->assertEquals('Summary', $entity->getSummary());
    }

    public function testCodeGetter()
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
        $this->assertEquals('ORO - 5', $issue->getCode());
    }

    public function testDescriptionSetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getDescription());
        $entity->setDescription('Description');
        $this->assertEquals('Description', $entity->getDescription());
    }

    public function testCreatedAtSetterGetter()
    {
        $date = new \DateTime();
        $entity = new Issue();
        $this->assertNull($entity->getCreatedAt());
        $entity->setCreatedAt($date);
        $this->assertEquals($date, $entity->getCreatedAt());
    }

    public function testUpdatedAtSetterGetter()
    {
        $date = new \DateTime();
        $entity = new Issue();
        $this->assertNull($entity->getUpdatedAt());
        $entity->setUpdatedAt($date);
        $this->assertEquals($date, $entity->getUpdatedAt());
    }

    public function testTypeSetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getType());
        $type = new IssueType();
        $entity->setType($type);
        $this->assertEquals($type, $entity->getType());
    }

    public function testReporterSetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getReporter());
        $user = new User();
        $entity->setReporter($user);
        $this->assertEquals($user, $entity->getReporter());
    }

    public function testOwnerSetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getOwner());
        $user = new User();
        $entity->setOwner($user);
        $this->assertEquals($user, $entity->getOwner());
    }

    public function testOrganizationSetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getOrganization());
        $organization = new Organization();
        $entity->setOrganization($organization);
        $this->assertEquals($organization, $entity->getOrganization());
    }

    public function testPrioritySetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getPriority());
        $priority = new IssuePriority();
        $entity->setPriority($priority);
        $this->assertEquals($priority, $entity->getPriority());
    }

    public function testResolutionSetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getResolution());
        $resolution = new IssueResolution();
        $entity->setResolution($resolution);
        $this->assertEquals($resolution, $entity->getResolution());
    }

    public function testParentSetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getParent());
        $parent = new Issue();
        $entity->setParent($parent);
        $this->assertEquals($parent, $entity->getParent());
    }

    public function testWorkflowStepSetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getWorkflowStep());
        $step = new WorkflowStep();
        $entity->setWorkflowStep($step);
        $this->assertEquals($step, $entity->getWorkflowStep());
    }

    public function testWorkflowItemSetterGetter()
    {
        $entity = new Issue();
        $this->assertNull($entity->getWorkflowItem());
        $item = new WorkflowItem();
        $entity->setWorkflowItem($item);
        $this->assertEquals($item, $entity->getWorkflowItem());
    }

    public function testTagsSetterGetter()
    {
        $entity = new Issue();
        $this->assertTrue($entity->getTags() instanceof ArrayCollection);
        $this->assertCount(0, $entity->getTags());
        $tags = new ArrayCollection();
        $tags->add('tag1');
        $tags->add('tag2');
        $entity->setTags($tags);
        $this->assertEquals($tags, $entity->getTags());
        $this->assertCount(2, $entity->getTags());
    }

    public function testChildrenGetter()
    {
        $entity = new Issue();
        $this->assertEquals(array(), $entity->getChildren());
    }

    public function testCollaboratorsGetter()
    {
        $entity = new Issue();
        $this->assertEquals(array(), $entity->getCollaborators());
    }

    public function testAddCollaboratorFunction()
    {
        $issue = new Issue();
        $this->assertCount(0, $issue->getCollaborators());
        $user = new User();
        $issue->addCollaborator($user);
        $this->assertCount(1, $issue->getCollaborators());
    }

    public function testRemoveCollaboratorFunction()
    {
        $issue = new Issue();
        $this->assertCount(0, $issue->getCollaborators());
        $user = new User();
        $issue->addCollaborator($user);
        $this->assertCount(1, $issue->getCollaborators());
        $issue->removeCollaborator($user);
        $this->assertCount(0, $issue->getCollaborators());
    }

    public function testHasCollaboratorFunction()
    {
        $issue = new Issue();

        $this->assertCount(0, $issue->getCollaborators());

        $user1 = new User();
        $user1->setEmail('email1@');

        $user2 = new User();
        $user2->setEmail('email2@');
        $issue->addCollaborator($user1);

        $this->assertCount(1, $issue->getCollaborators());
        $this->assertTrue($issue->hasCollaborator($user1));
        $this->assertFalse($issue->hasCollaborator($user2));
    }

    public function testToString()
    {
        $issue = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\Issue')
            ->setMethods(array('getCode'))
            ->getMock();
        $issue
            ->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue('ORO - 5'));

        $this->assertEquals('ORO - 5', (string)$issue);
    }

    public function testPrePersist()
    {
        $issue = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\Issue')
            ->setMethods(array('setCreatedAt', 'setUpdatedAt'))
            ->getMock();
        $issue
            ->expects($this->once())
            ->method('setCreatedAt')
            ->will($this->returnValue($issue));
        $issue
            ->expects($this->once())
            ->method('setUpdatedAt')
            ->will($this->returnValue($issue));

        $issue->prePersist();
    }

    public function testPreUpdate()
    {
        $issue = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\Issue')
            ->setMethods(array('setCreatedAt', 'setUpdatedAt'))
            ->getMock();
        $issue
            ->expects($this->never())
            ->method('setCreatedAt')
            ->will($this->returnValue($issue));
        $issue
            ->expects($this->once())
            ->method('setUpdatedAt')
            ->will($this->returnValue($issue));

        $issue->preUpdate();
    }
}
