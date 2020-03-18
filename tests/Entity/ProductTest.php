<?php


namespace App\Tests\Entity;


use App\Entity\Product;
use Monolog\Test\TestCase;

class ProductTest extends TestCase
{
    public function testSetStock()
    {
        $product = new Product();

        $product->setProductStock(10);
        $this->assertSame(10, $product->getProductStock());
    }
}