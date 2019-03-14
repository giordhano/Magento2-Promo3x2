<?php 

namespace Giodev\SalesRule\Model\Rule\Action\Discount;

class PromoBuyXGetY extends \Magento\SalesRule\Model\Rule\Action\Discount\AbstractDiscount {
    
    protected $discountFactory;   
    protected $productFactory;
    protected $priceCurrency;
    protected $cart;   

    public function __construct( 
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,  
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Checkout\Model\Cart $cart 
    )
    { 
        $this->discountFactory = $discountDataFactory;
        $this->productFactory = $productFactory; 
        $this->priceCurrency = $priceCurrency;
        $this->cart = $cart;   
        parent::__construct($validator, $discountDataFactory, $priceCurrency);
    }


    /**
     * Returns SKUS valid in offer 
     * @param \Magento\SalesRule\Model\Rule $rule 
     * @return string  
     */
    public function getRuleProductsSku($rule){
        
        $skusRule = array_map('trim',explode(',', $rule->getActionProducts()));
        return $skusRule;
    }
    
    /**
     * Get array ItemId => QtyToDiscount
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param int $qtyMinProducts      
     * @param int $qtyToDiscount      
     * @return array 
     */
    public function getItemsToDiscount($quote, $rule, $qtyMinProducts, $qtyToDiscount){
        
        $rule_id = $rule->getId();

        if($quote->hasData('_rule_data_promo_'.$rule_id)){
            return $quote->getData('_rule_data_promo_'.$rule_id);
        }
  
        $itemsPrice = [];
        $itemsQty = [];

        // Counter for the total products valid in offer
        $qtyTotalProductsInOffer = 0; 
 
        foreach ($quote->getAllVisibleItems() as $itemQuote) {
    
            $itemSku = $itemQuote->getProduct()->getSku();

            if($itemQuote->getProduct()->getTypeId() == 'configurable'){
                
                $product = $this->productFactory->create()->load($itemQuote->getProduct()->getId()); 
                $itemSku = $product->getSku();
            }
     
            //  We check if item is within the offer
            if(in_array($itemSku, $this->getRuleProductsSku($rule))){

                $qtyTotalProductsInOffer+=$itemQuote->getQty();
                $itemPrice = $itemQuote->getCalculationPrice();

                $itemsPrice[trim($itemQuote->getId())] = $itemPrice; 
                $itemsQty[trim($itemQuote->getId())] = $itemQuote->getQty();  
            } 
 
        } 
    
        $itemsQtyToDiscount = []; 
  
        if($qtyTotalProductsInOffer >= $qtyMinProducts){
            
            // Sort items by price from lowest to highest
            asort($itemsPrice); 
            $itemsID = array_keys($itemsPrice); 
                 
            $_totalQtyToDiscount = $qtyToDiscount;
            foreach ($itemsID as $itemId) {
                
                $qtyItem = $itemsQty[$itemId];

                if($qtyItem >= $_totalQtyToDiscount){
                    $itemsQtyToDiscount[$itemId] = [ 
                        'qty' => $_totalQtyToDiscount
                    ];

                    $_totalQtyToDiscount = 0;
                }
                else{

                    $itemsQtyToDiscount[$itemId] = [ 
                        'qty' => $qtyItem
                    ];

                    $_totalQtyToDiscount = $_totalQtyToDiscount - $qtyItem;
                }

                if($_totalQtyToDiscount <= 0) break; 
            }

        } 


        $quote->setData('_rule_data_promo_'.$rule_id, $itemsQtyToDiscount);
 
        return $itemsQtyToDiscount;     
    }
 
    /**
     * Calculate
     * @param \Magento\SalesRule\Model\Rule $rule 
     * @param\Magento\Quote\Model\Quote\Item $item 
     * @param double $qty
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data   
     */
    public function calculate($rule, $item, $qty)
    { 
 
        $discountData = $this->discountFactory->create();
        $itemPrice = $this->validator->getItemPrice($item);
        $baseItemPrice = $this->validator->getItemBasePrice($item);
        $itemOriginalPrice = $this->validator->getItemOriginalPrice($item);
        $baseItemOriginalPrice = $this->validator->getItemBaseOriginalPrice($item);
 
        $qtyMinProducts = (int) $rule->getDiscountStep();
        $cantidadDescontar = (int) $rule->getDiscountAmount();

        if(!is_numeric($qtyMinProducts) || !is_numeric($cantidadDescontar)){

            throw new \Exception("Invalid params", 1);
        }
 
        $itemsToDiscount = $this->getItemsToDiscount($this->cart->getQuote(), $rule, $qtyMinProducts, $cantidadDescontar);
 
        $idsPromoDiscount = array_keys($itemsToDiscount);
  
        if(in_array($item->getId(), $idsPromoDiscount)){

            $qtyToDiscount = $itemsToDiscount[$item->getId()]['qty'];
            
            $amountDiscount = $itemOriginalPrice * $qtyToDiscount;

            $discountData->setAmount($amountDiscount);
            $discountData->setBaseAmount($amountDiscount);
            $discountData->setOriginalAmount(($itemOriginalPrice * $qty));
            $discountData->setBaseOriginalAmount($this->priceCurrency->round($baseItemOriginalPrice));
  
        }
            
        return $discountData;
 
    }
 
}