<?php
namespace OroCRM\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Oro\Bundle\BtsBundle\Entity\Issue;
use Oro\Bundle\BtsBundle\Entity\IssueType;
use Oro\Bundle\BtsBundle\Entity\IssuePriority;

class LoadIssueData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var  TagManager */
    protected $tagManager;

    protected $issues = array(
        array(
            'summary'  => 'ORO academic project',
            'type'     => IssueType::STORY,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Install && configuration',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Add basic entities',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Unit tests for entities',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Add Issue controller and basic ui',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Tests for basic UI',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Add notes support',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Add tags to issue',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Add issue workflow',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Search Issue',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Import & Export',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Dashboards widgets',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'User page widget',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Email activity',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Demo data',
            'type'     => IssueType::SUBTASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Review',
            'type'     => IssueType::TASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Review fixes',
            'type'     => IssueType::TASK,
            'priority' => IssuePriority::CRITICAL,
            'reporter' => 'user_manager',
            'assignee' => 'user_user',
        ),
        array(
            'summary'  => 'Install ORO CRM',
            'type'     => IssueType::TASK,
            'priority' => IssuePriority::MAJOR,
            'reporter' => 'user_manager',
            'assignee' => 'user_admin',
        ),
    );

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'Oro\Bundle\BtsBundle\Migrations\Data\Demo\ORM\LoadUserData',
        ];
    }

    /**
     * Set container
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $organization = $this->getReference('default_organization');

        $users = array(
            'user_admin'   => $this->getReference('user_admin'),
            'user_manager' => $this->getReference('user_manager'),
            'user_user'    => $this->getReference('user_user'),
        );

        $issueTypeRepository     = $manager->getRepository('OroBundleBtsBundle:IssueType');
        $issuePriorityRepository = $manager->getRepository('OroBundleBtsBundle:IssuePriority');

        $types = array(
            IssueType::TASK    => $issueTypeRepository->findOneBy(['name' => IssueType::TASK]),
            IssueType::STORY   => $issueTypeRepository->findOneBy(['name' => IssueType::STORY]),
            IssueType::SUBTASK => $issueTypeRepository->findOneBy(['name' => IssueType::SUBTASK]),
        );

        $priorities = array(
            IssuePriority::MAJOR    => $issuePriorityRepository->findOneBy(['name' => IssuePriority::MAJOR]),
            IssuePriority::CRITICAL => $issuePriorityRepository->findOneBy(['name' => IssuePriority::CRITICAL]),
        );

        $story = null;

        foreach ($this->issues as $data) {
            $issue = new Issue();

            $issue->setOrganization($organization);
            $issue->setSummary($data['summary']);
            $issue->setDescription(sprintf('<p>Description for "%s"</p>', $data['summary']));
            $issue->setOwner($users[$data['assignee']]);
            $issue->setReporter($users[$data['reporter']]);
            $issue->setType($types[$data['type']]);
            $issue->setPriority($priorities[$data['priority']]);

            if (IssueType::STORY === $data['type']) {
                $story = $issue;
            }

            if (IssueType::SUBTASK === $data['type']) {
                if (null === $story) {
                    continue;
                }

                $issue->setParent($story);
            }

            $manager->persist($issue);
        }

        $manager->flush();
    }
}
