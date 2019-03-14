<?php 

namespace Giodev\SalesRule\Model\Rule;

class ActionBuyXGetYDiscountMinorPrice extends \Magento\Rule\Model\AbstractModel {
 
    const RULE_ACTION = 'buy_x_get_y_discount_cheapest_items';
  
    public function getConditionsInstance()
    {
        return $this->_condCombineFactory->create();
    } 
    
    public function getActionsInstance()
    {
        return $this->_condProdCombineF->create();
    }
}