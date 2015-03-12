<?php

namespace Oro\Bundle\BtsBundle\Entity\Repository;

use Oro\Bundle\BtsBundle\Entity\IssueType;

use Doctrine\ORM\EntityRepository;

class IssueRepository extends EntityRepository
{
    /**
     * Load list of issues grouped by status
     *
     * @return  array
     */
    public function loadIssuesGroupedByStatus()
    {
        $q = $this
            ->createQueryBuilder('issue')
            ->select('count(issue.id) AS cnt, step.label AS label')
            ->leftJoin('issue.workflowStep', 'step')
            ->groupBy('step.id')
            ->getQuery();

        return $q->getResult();
    }
}
