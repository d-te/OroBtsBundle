<?php

namespace Oro\Bundle\BtsBundle\Tests\Unit\EventListener;

use Oro\Bundle\BtsBundle\Entity\Issue;

class IssueListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testPrePersistIssue()
    {
        $issue = new Issue();

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->setMethods(array('getEntity'))
            ->getMock();

        $args->expects($this->once())->method('getEntity')->will($this->returnValue($issue));

        $listener = $this->getMockBuilder('Oro\Bundle\BtsBundle\EventListener\IssueListener')
            ->disableOriginalConstructor()
            ->setMethods(array('addIssueReporter'))
            ->getMock();

        $listener
            ->expects($this->once())
            ->method('addIssueReporter')
            ->with($this->equalTo($issue));

        $listener->prePersist($args);
    }

    public function testAddIssueReporter()
    {
        $issue = $this->getMockBuilder('Oro\Bundle\BtsBundle\Entity\Issue')
            ->setMethods(array('setReporter'))
            ->getMock();

        $issue
            ->expects($this->once())
            ->method('setReporter');

        $listener = $this->getMockBuilder('Oro\Bundle\BtsBundle\EventListener\IssueListener')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser'))
            ->getMock();

        $listener
            ->expects($this->once())
            ->method('getUser');

        $listener->addIssueReporter($issue);
    }
}
