<?php


namespace App\Tests\Entity;


use App\Entity\Product;
use Monolog\Test\TestCase;

class ProductTest extends TestCase
{
    public function testSettingProperty()
    {
        $product = new Product();

        $product->setProductStock(10);
        $this->assertSame(10, $product->getProductStock());
    }

    public function testNewProduct()
    {
        $product = (new Product())
            ->setProductCode('testcode')
            ->setProductName('testproduct')
            ->setProductDescription('testdesc')
            ->setProductStock(1)
            ->setNetCost(1)
            ->setIsDiscontinued('test');

        $this->assertSame('The product code should be: testcode The product name should be testproduct The product description should be: testdesc The cost should be: 1 The stock should be: 1 The products discontinued value should be: test', $product->getDetails() );
    }
}