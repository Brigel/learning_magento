<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magecom\AroundProducts\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.8', '<')) {

            $setup->startSetup();
            $connection = $setup->getConnection();
            $connection->addColumn(
                $setup->getTable('around_products'),
                'counter',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Amount of user\'s viewed'
                ]
            );
            $connection->changeColumn(
                'products_session_history',
                'session_id',
                'session_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 41,
                    'comment' => 'Session id'
                ]
            );
            $connection->addColumn(
                $setup->getTable('products_session_history'),
                'processed',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Is processed this item'
                ]
            );
            $connection->addIndex(
                'around_products',
                $connection->getIndexName('around_products', 'counter'),
                'counter');
            $connection->addIndex(
                'around_products',
                $connection->getIndexName('around_products', 'product_id_main'),
                'product_id_main');
            $connection->addIndex(
                'around_products',
                $connection->getIndexName('around_products', 'product_id'),
                'product_id');
            $connection->addIndex(
                'around_products',
                $connection->getIndexName('around_products', 'is_disable'),
                'is_disable');


            $connection->addIndex(
                'products_session_history',
                $connection->getIndexName('products_session_history', 'session_id'),
                'session_id');
            $connection->addIndex(
                'products_session_history',
                $connection->getIndexName('products_session_history', 'processed'),
                'processed');

            $setup->endSetup();

        }
    }
}
