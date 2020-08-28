<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @UniqueEntity("productCode", message="Product Code must be unique")
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
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private $productManufacture;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(10, message="Stock must not be less than 10")
     */
    private $productStock;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\GreaterThanOrEqual(5, message="Cost cannot be lower than £5")
     * @Assert\LessThanOrEqual(1000, message="Cost cannot be greater than £1000")
     */
    private $netCost;

    /**
     * @ORM\Column(type="string")
     */
    private $taxRate = 20;

    /**
     * @ORM\Column(type="string")
     */
    private $isDiscontinued;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSuccessful = true;

    /**
     * @ORM\Column(type="string")
     */
    private $reasonsForFailure = 'N/A';

    /**
     * @return string
     */
    public function getReasonsForFailure(): string
    {
        return $this->reasonsForFailure;
    }

    /**
     * @param string $reasonsForFailure
     */
    public function setReasonsForFailure(string $reasonsForFailure): void
    {
        $this->reasonsForFailure = $reasonsForFailure;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @param bool $isSuccessful
     */
    public function setIsSuccessful(bool $isSuccessful): void
    {
        $this->isSuccessful = $isSuccessful;
    }

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

    public function setProductStock(int $productStock): ?self
    {
        $this->productStock = $productStock;

        return $this;
    }

    public function getNetCost(): ?float
    {
        return $this->netCost;
    }

    public function setNetCost(float $netCost): self
    {
        $this->netCost = $netCost;

        return $this;
    }

    public function getTaxRate(): ?string
    {
        return $this->taxRate;
    }

    public function setTaxRate(string $taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getIsDiscontinued(): ?string
    {
        return $this->isDiscontinued;
    }

    public function setIsDiscontinued(string $isDiscontinued): ?self
    {
        $this->isDiscontinued = $isDiscontinued;

        return $this;
    }

    public function getDetails() // This get method is used for testing purposes.
    {
        return sprintf(
          'The product code should be: %s The product name should be %s The product description should be: %s The cost should be: %s The stock should be: %s The products discontinued value should be: %s',
            $this->getProductCode(),
            $this->getProductName(),
            $this->getProductDescription(),
            $this->getNetCost(),
            $this->getProductStock(),
            $this->getIsDiscontinued()
        );
    }
}
