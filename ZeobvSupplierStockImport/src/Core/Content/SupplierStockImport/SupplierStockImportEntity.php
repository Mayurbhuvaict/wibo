<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Core\Content\SupplierStockImport;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Content\Product\ProductEntity;

class SupplierStockImportEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $productId;

    /**
     * @var string|null
     */
    protected $eanNumber;

    /**
     * @var string|null
     */
    protected $atagApiRecord;

    /**
     * @var string|null
     */
    protected $etnaApiRecord;

    /**
     * @var string|null
     */
    protected $pelgrimApiRecord;

    /**
     * @var string|null
     */
    protected $hisenseApiRecord;

    /**
     * @var string|null
     */
    protected $askoApiRecord;

    /**
     * @var string|null
     */
    protected $amacomApiRecord;

    /**
     * @var string|null
     */
    protected $borettiApiRecord;

    /**
     * @var string|null
     */
    protected $inventumApiRecord;

    /**
     * @var string|null
     */
    protected $smegApiRecord;

    /**
     * @var \DateTimeInterface|null
     */
    protected $lastUsageAt;

    /**
     * @var ProductEntity|null
     */
    protected $product;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getEanNumber(): ?string
    {
        return $this->eanNumber;
    }

    public function setEanNumber(?string $eanNumber): void
    {
        $this->eanNumber = $eanNumber;
    }

    public function getAtagApiRecord(): ?string
    {
        return $this->atagApiRecord;
    }

    public function setAtagApiRecord(?string $atagApiRecord): void
    {
        $this->atagApiRecord = $atagApiRecord;
    }

    public function getEtnaApiRecord(): ?string
    {
        return $this->etnaApiRecord;
    }

    public function setEtnaApiRecord(?string $etnaApiRecord): void
    {
        $this->etnaApiRecord = $etnaApiRecord;
    }

    public function getPelgrimApiRecord(): ?string
    {
        return $this->pelgrimApiRecord;
    }

    public function setPelgrimApiRecord(?string $pelgrimApiRecord): void
    {
        $this->pelgrimApiRecord = $pelgrimApiRecord;
    }

    public function getHisenseApiRecord(): ?string
    {
        return $this->hisenseApiRecord;
    }

    public function setHisenseApiRecord(?string $hisenseApiRecord): void
    {
        $this->hisenseApiRecord = $hisenseApiRecord;
    }

    public function getAskoApiRecord(): ?string
    {
        return $this->askoApiRecord;
    }

    public function setAskoApiRecord(?string $askoApiRecord): void
    {
        $this->askoApiRecord = $askoApiRecord;
    }

    public function getAmacomApiRecord(): ?string
    {
        return $this->amacomApiRecord;
    }

    public function setAmacomApiRecord(?string $amacomApiRecord): void
    {
        $this->amacomApiRecord = $amacomApiRecord;
    }

    public function getBorettiApiRecord(): ?string
    {
        return $this->borettiApiRecord;
    }

    public function setBorettiApiRecord(?string $borettiApiRecord): void
    {
        $this->borettiApiRecord = $borettiApiRecord;
    }

    public function getInventumApiRecord(): ?string
    {
        return $this->inventumApiRecord;
    }

    public function setInventumApiRecord(?string $inventumApiRecord): void
    {
        $this->inventumApiRecord = $inventumApiRecord;
    }

    public function getSmegApiRecord(): ?string
    {
        return $this->smegApiRecord;
    }

    public function setSmegApiRecord(?string $smegApiRecord): void
    {
        $this->smegApiRecord = $smegApiRecord;
    }

    public function getLastUsageAt(): ?\DateTimeInterface
    {
        return $this->lastUsageAt;
    }

    public function setLastUsageAt(?\DateTimeInterface $lastUsageAt): void
    {
        $this->lastUsageAt = $lastUsageAt;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}