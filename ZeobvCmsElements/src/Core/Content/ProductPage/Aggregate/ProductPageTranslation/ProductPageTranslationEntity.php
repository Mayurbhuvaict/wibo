<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\ProductPage\Aggregate\ProductPageTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\System\Language\LanguageEntity;

class ProductPageTranslationEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $mainTitle;

    /**
     * @var string|null
     */
    protected $mainDescription;

    /**
     * @var string|null
     */
    protected $subTitleOne;

    /**
     * @var string|null
     */
    protected $subDescriptionOne;

    /**
     * @var string|null
     */
    protected $subTitleTwo;

    /**
     * @var string|null
     */
    protected $subDescriptionTwo;

    /**
     * @var string|null
     */
    protected $subTitleThree;

    /**
     * @var string|null
     */
    protected $subDescriptionThree;

    /**
     * @var string|null
     */
    protected $subTitleFour;

    /**
     * @var string|null
     */
    protected $subDescriptionFour;

    /**
     * @var string|null
     */
    protected $subTitleFive;

    /**
     * @var string|null
     */
    protected $subDescriptionFive;

    /**
     * @var string|null
     */
    protected $subTitleSix;

    /**
     * @var string|null
     */
    protected $subDescriptionSix;

    /**
     * @var string|null
     */
    protected $subTitleSeven;

    /**
     * @var string|null
     */
    protected $subDescriptionSeven;

    /**
     * @var string|null
     */
    protected $subTitleEight;

    /**
     * @var string|null
     */
    protected $subDescriptionEight;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $productPageId;

    /**
     * @var string
     */
    protected $languageId;

    /**
     * @var ArrayEntity|null
     */
    protected $productPage;

    /**
     * @var LanguageEntity|null
     */
    protected $language;

    public function getMainTitle(): string
    {
        return $this->mainTitle;
    }

    public function setMainTitle(string $mainTitle): void
    {
        $this->mainTitle = $mainTitle;
    }

    public function getMainDescription(): ?string
    {
        return $this->mainDescription;
    }

    public function setMainDescription(?string $mainDescription): void
    {
        $this->mainDescription = $mainDescription;
    }

    public function getSubTitleOne(): ?string
    {
        return $this->subTitleOne;
    }

    public function setSubTitleOne(?string $subTitleOne): void
    {
        $this->subTitleOne = $subTitleOne;
    }

    public function getSubDescriptionOne(): ?string
    {
        return $this->subDescriptionOne;
    }

    public function setSubDescriptionOne(?string $subDescriptionOne): void
    {
        $this->subDescriptionOne = $subDescriptionOne;
    }

    public function getSubTitleTwo(): ?string
    {
        return $this->subTitleTwo;
    }

    public function setSubTitleTwo(?string $subTitleTwo): void
    {
        $this->subTitleTwo = $subTitleTwo;
    }

    public function getSubDescriptionTwo(): ?string
    {
        return $this->subDescriptionTwo;
    }

    public function setSubDescriptionTwo(?string $subDescriptionTwo): void
    {
        $this->subDescriptionTwo = $subDescriptionTwo;
    }

    public function getSubTitleThree(): ?string
    {
        return $this->subTitleThree;
    }

    public function setSubTitleThree(?string $subTitleThree): void
    {
        $this->subTitleThree = $subTitleThree;
    }

    public function getSubDescriptionThree(): ?string
    {
        return $this->subDescriptionThree;
    }

    public function setSubDescriptionThree(?string $subDescriptionThree): void
    {
        $this->subDescriptionThree = $subDescriptionThree;
    }

    public function getSubTitleFour(): ?string
    {
        return $this->subTitleFour;
    }

    public function setSubTitleFour(?string $subTitleFour): void
    {
        $this->subTitleFour = $subTitleFour;
    }

    public function getSubDescriptionFour(): ?string
    {
        return $this->subDescriptionFour;
    }

    public function setSubDescriptionFour(?string $subDescriptionFour): void
    {
        $this->subDescriptionFour = $subDescriptionFour;
    }

    public function getSubTitleFive(): ?string
    {
        return $this->subTitleFive;
    }

    public function setSubTitleFive(?string $subTitleFive): void
    {
        $this->subTitleFive = $subTitleFive;
    }

    public function getSubDescriptionFive(): ?string
    {
        return $this->subDescriptionFive;
    }

    public function setSubDescriptionFive(?string $subDescriptionFive): void
    {
        $this->subDescriptionFive = $subDescriptionFive;
    }

    public function getSubTitleSix(): ?string
    {
        return $this->subTitleSix;
    }

    public function setSubTitleSix(?string $subTitleSix): void
    {
        $this->subTitleSix = $subTitleSix;
    }

    public function getSubDescriptionSix(): ?string
    {
        return $this->subDescriptionSix;
    }

    public function setSubDescriptionSix(?string $subDescriptionSix): void
    {
        $this->subDescriptionSix = $subDescriptionSix;
    }

    public function getSubTitleSeven(): ?string
    {
        return $this->subTitleSeven;
    }

    public function setSubTitleSeven(?string $subTitleSeven): void
    {
        $this->subTitleSeven = $subTitleSeven;
    }

    public function getSubDescriptionSeven(): ?string
    {
        return $this->subDescriptionSeven;
    }

    public function setSubDescriptionSeven(?string $subDescriptionSeven): void
    {
        $this->subDescriptionSeven = $subDescriptionSeven;
    }

    public function getSubTitleEight(): ?string
    {
        return $this->subTitleEight;
    }

    public function setSubTitleEight(?string $subTitleEight): void
    {
        $this->subTitleEight = $subTitleEight;
    }

    public function getSubDescriptionEight(): ?string
    {
        return $this->subDescriptionEight;
    }

    public function setSubDescriptionEight(?string $subDescriptionEight): void
    {
        $this->subDescriptionEight = $subDescriptionEight;
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

    public function getProductPageId(): string
    {
        return $this->productPageId;
    }

    public function setProductPageId(string $productPageId): void
    {
        $this->productPageId = $productPageId;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function getProductPage(): ?ArrayEntity
    {
        return $this->productPage;
    }

    public function setProductPage(?ArrayEntity $productPage): void
    {
        $this->productPage = $productPage;
    }

    public function getLanguage(): ?LanguageEntity
    {
        return $this->language;
    }

    public function setLanguage(?LanguageEntity $language): void
    {
        $this->language = $language;
    }
}