<?php

namespace Oro\Bundle\BtsBundle\Entity\Repository;

use Oro\Bundle\BtsBundle\Entity\IssueType;

use Doctrine\ORM\EntityRepository;

class IssueTypeRepository extends EntityRepository
{
    /**
     * Load list of tasks without subtask
     *
     * @return  array
     */
    public function loadTypesWithoutSubtask()
    {
        return $this->loadTypesWithoutSubtaskQueryBuilder()->getQuery()->getResult();
    }

    /**
     * Query builder for loading list of tasks without subtask
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function loadTypesWithoutSubtaskQueryBuilder()
    {
        $q = $this
            ->createQueryBuilder('type')
            ->where('type.name != :name')
            ->setParameter('name', IssueType::SUBTASK)
            ->orderBy('type.order', 'DESC');

        return $q;
    }
}
