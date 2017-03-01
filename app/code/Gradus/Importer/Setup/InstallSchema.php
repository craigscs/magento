<?php

namespace Gradus\Importer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('custom_imports')
        )->addColumn(
            'import_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true, 'nullable' => false, 'primary' => true),
            'Import ID'
        )->addColumn(
            'messages',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            array('nullable' => false),
            'Messages'
        )->addColumn(
            'process',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            array('nullable' => false),
            'Process'
        )->addColumn(
            'start',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            array(),
            'Start Time'
        )->addColumn(
            'end',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            array(),
            'End Time'
        )->addColumn(
            'succeeded',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            array(),
            'Succeeded'
        )->setComment(
            'Imports Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();

    }
}
