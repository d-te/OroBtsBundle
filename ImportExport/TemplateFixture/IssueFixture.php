<?php

namespace Oro\Bundle\BtsBundle\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;

use Oro\Bundle\BtsBundle\Entity\Issue;
use Oro\Bundle\BtsBundle\Entity\IssuePriority;
use Oro\Bundle\BtsBundle\Entity\IssueResolution;
use Oro\Bundle\BtsBundle\Entity\IssueType;

class IssueFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return 'Oro\Bundle\BtsBundle\Entity\Issue';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->getEntityData('Issue-1');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity($key)
    {
        return new Issue();
    }

    /**
     * @param string $key
     * @param object $entity
     */
    public function fillEntityData($key, $entity)
    {
        $user = $this->templateManager
            ->getEntityRepository('Oro\Bundle\UserBundle\Entity\User')
            ->getEntity('John Dow');

        $organization = $this->templateManager
            ->getEntityRepository('Oro\Bundle\OrganizationBundle\Entity\Organization')
            ->getEntity('default');

        $type = new IssueType();
        $type->setName(IssueType::TASK);

        $priority = new IssuePriority();
        $priority->setName(IssuePriority::MAJOR);

        $resolution = new IssueResolution();
        $resolution->setName(IssueResolution::FIXED);

        switch ($key) {
            case 'Issue-1':
                $entity->setSummary('Summary field');
                $entity->setDescription('Description field');
                $entity->setOrganization($organization);
                $entity->setType($type);
                $entity->setPriority($priority);
                $entity->setResolution($resolution);
                $entity->setReporter($user);
                $entity->setOwner($user);
                $entity->createdAt(new \DateTime());
                $entity->updatedAt(new \DateTime());
                return;
        }
        parent::fillEntityData($key, $entity);
    }
}
