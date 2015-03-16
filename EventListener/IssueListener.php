<?php
namespace Oro\Bundle\BtsBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Oro\Bundle\BtsBundle\Entity\Issue;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class IssueListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get Security context
     *
     * @return SecurityContextInterface
     */
    protected function getSecurityContext()
    {
        if (!$this->securityContext) {
            $this->securityContext = $this->container->get('security.context');
        }

        return $this->securityContext;
    }

    /**
     * Get current User
     *
     * @return \Oro\Bundle\BtsBundle\Entity\User
     */
    public function getUser()
    {
        return $this->getSecurityContext()->getToken()->getUser();
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Issue) {
            $this->addIssueReporter($entity);
        }
    }

    /**
     * Add issue reporter
     *
     * @param Issue $issue
     */
    public function addIssueReporter(Issue $issue)
    {
        if (null === $issue->getReporter()) {
            $issue->setReporter($this->getUser());
        }
    }
}
