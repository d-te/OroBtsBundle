<?php

namespace Oro\Bundle\BtsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;

/**
 * Workflow steps
 *
 */
class IssueWorkflowStep extends WorkflowStep
{
    const CLOSED = 'closed';
    const OPENED = 'opened';
}
