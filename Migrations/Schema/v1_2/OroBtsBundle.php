<?php

namespace Oro\Bundle\BtsBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @codeCoverageIgnore
 */
class OroBtsBundle implements Migration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->addWorkflowFields($schema);
        $this->addWorkflowForeignKeys($schema);
    }

    /**
     * Alter Issue table - adding workflow fields
     *
     * @param Schema $schema
     */
    public function addWorkflowFields(Schema $schema)
    {
        $table = $schema->getTable('oro_bts_issue');

        $table->addColumn('workflow_step_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_item_id', 'integer', ['notnull' => false]);

        $table->addIndex(['workflow_step_id'], 'IDX_41717B125FD201', []);
        $table->addUniqueIndex(['workflow_item_id'], 'UNIQ_41717B1B56C299');
    }

    /**
     * Add Workflow foreign keys.
     *
     * @param Schema $schema
     */
    public function addWorkflowForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('oro_bts_issue');

        $table->addForeignKeyConstraint(
            $schema->getTable('oro_workflow_step'),
            ['workflow_step_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('oro_workflow_item'),
            ['workflow_item_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }
}
