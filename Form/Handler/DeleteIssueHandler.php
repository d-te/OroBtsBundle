<?php

namespace Oro\Bundle\BtsBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;

use Oro\Bundle\SecurityBundle\Exception\ForbiddenException;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Handler\DeleteHandler as BaseDeleteHandler;

use Oro\Bundle\BtsBundle\Entity\Issue;
use Oro\Bundle\BtsBundle\Exception\IssueNotClosedException;

class DeleteIssueHandler extends BaseDeleteHandler
{
    /**
     * {@inheritdoc}
     */
    public function handleDelete($id, ApiEntityManager $manager)
    {
        $entity = $manager->find($id);
        if (!$entity) {
            throw new EntityNotFoundException();
        }

        $em = $manager->getObjectManager();
        $this->checkPermissions($entity, $em);

        foreach ($entity->getChildren() as $child) {
            $this->deleteEntity($child, $em);
        }

        $this->deleteEntity($entity, $em);

        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function checkPermissions($entity, ObjectManager $em)
    {
        parent::checkPermissions($entity, $em);

        if (!$entity->getModel()->isDeletable()) {
            throw new IssueNotClosedException();
        }
    }
}
