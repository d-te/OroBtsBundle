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
        $this->createIssueResolutionTable($schema);
        $this->createIssueResolutionTranslationsTable($schema);
        $this->createIssuePriorityTable($schema);
        $this->createIssuePriorityTranslationsTable($schema);
        $this->createIssueTypeTable($schema);
        $this->createIssueTypeTranslationsTable($schema);

        $this->createIssueTable($schema);
        $this->createCollaboratorsTable($schema);

        $this->addIssueForeignKeys($schema);
        $this->addCollaboratorsForeignKeys($schema);
    }

    /**
     * Create IssueResolution table
     *
     * @param Schema $schema
     */
    public function createIssueResolutionTable(Schema $schema)
    {
        $table = $schema->createTable('oro_bts_resolution');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('order', 'integer', []);

        $table->setPrimaryKey(['id']);
    }

    /**
     * Create IssueResolutionTranslations table
     *
     * @param Schema $schema
     */
    public function createIssueResolutionTranslationsTable(Schema $schema)
    {
        $table = $schema->createTable('oro_bts_resolution_translation');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 16]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('object_class', 'string', ['length' => 255]);
        $table->addColumn('field', 'string', ['length' => 32]);

        $table->setPrimaryKey(['id']);

        $table->addIndex(
            ['locale', 'object_class', 'field', 'foreign_key'],
            'oro_bts_resolution_translation_idx',
            []
        );
    }

    /**
     * Create IssuePriority table
     *
     * @param Schema $schema
     */
    public function createIssuePriorityTable(Schema $schema)
    {
        $table = $schema->createTable('oro_bts_priority');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('order', 'integer', []);

        $table->setPrimaryKey(['id']);
    }

    /**
     * Create IssuePriorityTranslations table
     *
     * @param Schema $schema
     */
    public function createIssuePriorityTranslationsTable(Schema $schema)
    {
        $table = $schema->createTable('oro_bts_priority_translation');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 16]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('object_class', 'string', ['length' => 255]);
        $table->addColumn('field', 'string', ['length' => 32]);

        $table->setPrimaryKey(['id']);

        $table->addIndex(
            ['locale', 'object_class', 'field', 'foreign_key'],
            'oro_bts_priority_translation_idx',
            []
        );
    }

    /**
     * Create IssueType table
     *
     * @param Schema $schema
     */
    public function createIssueTypeTable(Schema $schema)
    {
        $table = $schema->createTable('oro_bts_type');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('order', 'integer', []);

        $table->setPrimaryKey(['id']);
    }

    /**
     * Create IssueTypeTranslations table
     *
     * @param Schema $schema
     */
    public function createIssueTypeTranslationsTable(Schema $schema)
    {
        $table = $schema->createTable('oro_bts_type_translation');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 16]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('object_class', 'string', ['length' => 255]);
        $table->addColumn('field', 'string', ['length' => 32]);

        $table->setPrimaryKey(['id']);

        $table->addIndex(
            ['locale', 'object_class', 'field', 'foreign_key'],
            'oro_bts_type_translation_idx',
            []
        );
    }

    /**
     * Create Issue Table
     *
     * @param Schema $schema
     */
    public function createIssueTable(Schema $schema)
    {
        $table = $schema->createTable('oro_bts_issue');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('type_id', 'integer', ['notnull' => false]);
        $table->addColumn('priority_id', 'integer', ['notnull' => false]);
        $table->addColumn('resolution_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('summary', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', []);

        $table->setPrimaryKey(['id']);

        $table->addIndex(['parent_id'], 'IDX_41717B198BF200', []);
        $table->addIndex(['type_id'], 'IDX_41717B198BFDD1', []);
        $table->addIndex(['priority_id'], 'IDX_41717B198BF332', []);
        $table->addIndex(['resolution_id'], 'IDX_41717B198BF553', []);
        $table->addIndex(['reporter_id'], 'IDX_41717B198BF764', []);
        $table->addIndex(['owner_id'], 'IDX_41717B1913224', []);
        $table->addIndex(['organization_id'], 'IDX_41717BF453761', []);
    }

    /**
     * Create IssueCollaborators table
     *
     * @param Schema $schema
     */
    protected function createCollaboratorsTable(Schema $schema)
    {
        $table = $schema->createTable('oro_bts_collaborators');
        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('user_id', 'integer', []);

        $table->setPrimaryKey(['issue_id', 'user_id']);

        $table->addIndex(['user_id'], 'IDX_AA899B83D32F37', []);
        $table->addIndex(['issue_id'], 'IDX_AA899B83D3256', []);
    }

    /**
     * Add Issue foreign keys.
     *
     * @param Schema $schema
     */
    protected function addIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('oro_bts_issue');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_bts_issue'),
            ['parent_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_bts_resolution'),
            ['resolution_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_bts_priority'),
            ['priority_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_bts_type'),
            ['type_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['reporter_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['owner_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * Add IssueCollaborators foreign keys.
     *
     * @param Schema $schema
     */
    protected function addCollaboratorsForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('oro_bts_collaborators');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_bts_issue'),
            ['issue_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
    }
}