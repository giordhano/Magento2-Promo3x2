<?php
 
namespace GioDev\SalesRule\Test\Unit;
 
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
 
class PromoBuyXGetYTest extends \PHPUnit\Framework\TestCase
{
    protected $PromoBuyXGetY;

    public function setUp()
    {
        $objectManagerHelper = new ObjectManagerHelper($this);
 
        $validatorMock = $this->createMock(\Magento\SalesRule\Model\Validator::class);
        $discountDataFactoryMock = $this->createMock(\Magento\SalesRule\Model\Rule\Action\Discount\DataFactory::class);
        $priceCurrencyMock = $this->createMock(\Magento\Framework\Pricing\PriceCurrencyInterface::class);
        $productFactoryMock = $this->createMock(\Magento\Catalog\Model\ProductFactory::class);
        $cartMock = $this->createMock(\Magento\Checkout\Model\Cart::class);
  
        $this->PromoBuyXGetY = $objectManagerHelper->getObject(
            \Giodev\SalesRule\Model\Rule\Action\Discount\PromoBuyXGetY::class,
            [
                'validator' => $validatorMock,
                'discountDataFactory' => $discountDataFactoryMock,
                'priceCurrency' => $priceCurrencyMock,
                'productFactory' => $productFactoryMock,
                'cart' => $cartMock 
            ]
        );
 
    } 

    public function testGetItemsToDiscount(){

        $qtyMinProductos = 4;
        $qtyToDiscount = 2;

        $rule_id = 1;

        $quoteMock = $this->createPartialMock(
            \Magento\Quote\Model\Quote::class,
            ['getAllVisibleItems']
        );   
        
        $quoteItemMock1 = $this->createPartialMock(\Magento\Quote\Model\Quote\Item::class, ['getId','getProduct','getQty','getCalculationPrice']); 
        $productMock1 = $this->createMock(\Magento\Catalog\Model\Product::class);  
        $productMock1->expects($this->any())->method('getSku')->willReturn('SKU-simple-1');
        $productMock1->expects($this->any())->method('getTypeId')->willReturn('simple');  
        $quoteItemMock1->expects($this->any())->method('getId')->willReturn(1);
        $quoteItemMock1->expects($this->any())->method('getProduct')->willReturn($productMock1);
        $quoteItemMock1->expects($this->any())->method('getQty')->willReturn(2);
        $quoteItemMock1->expects($this->any())->method('getCalculationPrice')->willReturn(60);


        $quoteItemMock2 = $this->createPartialMock(\Magento\Quote\Model\Quote\Item::class, ['getId','getProduct','getQty','getCalculationPrice']); 
        $productMock2 = $this->createMock(\Magento\Catalog\Model\Product::class);  
        $productMock2->expects($this->any())->method('getSku')->willReturn('SKU-simple-2');
        $productMock2->expects($this->any())->method('getTypeId')->willReturn('simple');    
        $quoteItemMock2->expects($this->any())->method('getId')->willReturn(2);
        $quoteItemMock2->expects($this->any())->method('getProduct')->willReturn($productMock2);
        $quoteItemMock2->expects($this->any())->method('getQty')->willReturn(2);
        $quoteItemMock2->expects($this->any())->method('getCalculationPrice')->willReturn(20);


        $quoteItemMock3 = $this->createPartialMock(\Magento\Quote\Model\Quote\Item::class, ['getId','getProduct','getQty','getCalculationPrice']); 
        $productMock3 = $this->createMock(\Magento\Catalog\Model\Product::class);  
        $productMock3->expects($this->any())->method('getSku')->willReturn('SKU-simple-3');
        $productMock3->expects($this->any())->method('getTypeId')->willReturn('simple');   
        $quoteItemMock3->expects($this->any())->method('getId')->willReturn(3);
        $quoteItemMock3->expects($this->any())->method('getProduct')->willReturn($productMock3);
        $quoteItemMock3->expects($this->any())->method('getQty')->willReturn(2);
        $quoteItemMock3->expects($this->any())->method('getCalculationPrice')->willReturn(85);

        $quoteMock->expects($this->once())->method('getAllVisibleItems')->willReturn([$quoteItemMock1, $quoteItemMock2, $quoteItemMock3]);

        $ruleMock =  $this->createPartialMock(\Magento\SalesRule\Model\Rule::class, ['getId','getActionProducts']);   
        $ruleMock->expects($this->any())->method('getId')->willReturn($rule_id);
        $ruleMock->expects($this->any())->method('getActionProducts')->willReturn('SKU-simple-1,SKU-simple-3');
 
        $expectedResult = [
           "1" => ['qty' => 2]
        ];
        
        $this->assertEquals($expectedResult, $this->PromoBuyXGetY->getItemsToDiscount($quoteMock, $ruleMock, $qtyMinProductos, $qtyToDiscount) );

    }
 
}