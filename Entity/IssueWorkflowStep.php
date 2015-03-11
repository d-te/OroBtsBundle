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
    const OPENED             = 'opened';
    const IN_PROGRESS        = 'inprogress';
    const RESOLVED           = 'resolved';
    const CLOSED             = 'closed';

    const START_TRANSITION   = 'start';
    const STOP_TRANSITION    = 'stop';
    const RESOLVE_TRANSITION = 'resolve';
    const CLOSE_TRANSITION   = 'close';
    const REOPEN_TRANSITION  = 'reopen';
}
