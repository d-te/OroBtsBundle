<?php

namespace Oro\Bundle\BtsBundle\Model;

use Oro\Bundle\BtsBundle\Entity\Issue as Entity;
use Oro\Bundle\BtsBundle\Entity\IssueType;

class Issue
{
    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @param Entity $entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return sprintf('%s - %d', $this->entity->getOrganization()->getName(), $this->entity->getId());
    }

    /**
     * Check is issue is a strory
     *
     * @return bool
     */
    public function isStory()
    {
        return null !== $this->entity->getType() && IssueType::STORY === $this->entity->getType()->getName();
    }

    /**
     * Check is issue is a subtask
     *
     * @return bool
     */
    public function isSubtask()
    {
        return null !== $this->entity->getType() && IssueType::SUBTASK === $this->entity->getType()->getName();
    }

    /**
     * Check if issue can be deleted
     *
     * @return  bool
     */
    public function isDeletable()
    {
        $isDeletable = true;

        //TODO to be  implemented in Workflow
        return $isDeletable;

    }
}
