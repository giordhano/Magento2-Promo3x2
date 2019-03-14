<?PHP

namespace Giodev\SalesRule\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
 
       $setup->startSetup();
   
       if (version_compare( $context->getVersion(), '1.0.1' ) < 0) { 

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

       }
 
       $setup->endSetup();
    
    }
}
