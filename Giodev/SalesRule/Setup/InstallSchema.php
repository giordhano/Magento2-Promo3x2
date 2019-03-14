<?php

namespace Giodev\Salesrule\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
  
    public function install(SchemaSetupInterface $setup, 
                            ModuleContextInterface $context)
    {
 
        $setup->startSetup();
 
        $connection = $setup->getConnection();
         
        $setup->getConnection()->addColumn(
            $setup->getTable('salesrule'),
            'action_products',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'default' => null,
                'comment' => '.'
            ]
        );

        $setup->endSetup();
 
    }
}
