<?php

namespace Oro\Bundle\BtsBundle\Model;

use Oro\Bundle\BtsBundle\Entity\Issue as Entity;

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
     * @return string
     */
    public function getCode()
    {
        return sprintf('%s - %d', $this->entity->getOrganization()->getName(), $this->entity->getId());
    }
}
