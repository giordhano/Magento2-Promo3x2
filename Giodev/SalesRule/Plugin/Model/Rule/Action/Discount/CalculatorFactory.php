<?php 

namespace Giodev\SalesRule\Plugin\Model\Rule\Action\Discount;

class CalculatorFactory {

    protected $classByType = [
        \Giodev\SalesRule\Model\Rule\ActionBuyXGetYDiscountMinorPrice::RULE_ACTION => 'Giodev\SalesRule\Model\Rule\Action\Discount\PromoBuyXGetY'
    ]; 

    public function aroundCreate(
        \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $subject, 
        \Closure $proceed, 
        $type
    )
    {
        if ($type == \Giodev\SalesRule\Model\Rule\ActionBuyXGetYDiscountMinorPrice::RULE_ACTION) { 
            return \Magento\Framework\App\ObjectManager::getInstance()->create($this->classByType[$type]);
        }
        
        return $proceed($type);
    }
}