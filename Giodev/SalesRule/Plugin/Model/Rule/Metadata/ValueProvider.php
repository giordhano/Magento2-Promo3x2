<?php 

namespace Giodev\SalesRule\Plugin\Model\Rule\Metadata;

class ValueProvider {
 
    public function afterGetMetadataValues(
        \Magento\SalesRule\Model\Rule\Metadata\ValueProvider $provider, 
        $result
        )
    {
        $result['actions']['children']['simple_action']['arguments']['data']['config']['options'][] = array(
            'label' => 'Buy X and Get Y discounting the cheapest items',
            'value' => \Giodev\SalesRule\Model\Rule\ActionBuyXGetYDiscountMinorPrice::RULE_ACTION
        );
        return $result;
    }
}