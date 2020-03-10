<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productDescription;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productManufacture;

    /**
     * @ORM\Column(type="integer")
     */
    private $productStock;

    /**
     * @ORM\Column(type="integer")
     */
    private $netCost;

    /**
     * @ORM\Column(type="integer")
     */
    private $taxRate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDiscontinued;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): self
    {
        $this->productCode = $productCode;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getProductDescription(): ?string
    {
        return $this->productDescription;
    }

    public function setProductDescription(string $productDescription): self
    {
        $this->productDescription = $productDescription;

        return $this;
    }

    public function getProductManufacture(): ?string
    {
        return $this->productManufacture;
    }

    public function setProductManufacture(string $productManufacture): self
    {
        $this->productManufacture = $productManufacture;

        return $this;
    }

    public function getProductStock(): ?int
    {
        return $this->productStock;
    }

    public function setProductStock(int $productStock): self
    {
        $this->productStock = $productStock;

        return $this;
    }

    public function getNetCost(): ?int
    {
        return $this->netCost;
    }

    public function setNetCost(int $netCost): self
    {
        $this->netCost = $netCost;

        return $this;
    }

    public function getTaxRate(): ?int
    {
        return $this->taxRate;
    }

    public function setTaxRate(int $taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getIsDiscontinued(): ?bool
    {
        return $this->isDiscontinued;
    }

    public function setIsDiscontinued(bool $isDiscontinued): self
    {
        $this->isDiscontinued = $isDiscontinued;

        return $this;
    }
}
