<?php


namespace Zeobv\Quotations\Core\Content\Quote;


use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class QuoteEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $quoteNumber;

    /**
     * @var string
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $quotationStateId;

    /**
     * @var \DateTimeInterface
     */
    protected $expiryDate;

    /**
     * @return string
     */
    public function getQuoteNumber(): string
    {
        return $this->quoteNumber;
    }

    /**
     * @param string $quoteNumber
     * @return QuoteEntity
     */
    public function setQuoteNumber(string $quoteNumber): QuoteEntity
    {
        $this->quoteNumber = $quoteNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return QuoteEntity
     */
    public function setOrderId(string $orderId): QuoteEntity
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrder(): OrderEntity
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return QuoteEntity
     */
    public function setOrder(OrderEntity $orderId): QuoteEntity
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuotationStateId(): string
    {
        return $this->quotationStateId;
    }

    /**
     * @param string $quotationStateId
     * @return QuoteEntity
     */
    public function setQuotationStateId(string $quotationStateId): QuoteEntity
    {
        $this->quotationStateId = $quotationStateId;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getExpiryDate(): \DateTimeInterface
    {
        return $this->expiryDate;
    }

    /**
     * @param \DateTimeInterface $expiryDate
     * @return QuoteEntity
     */
    public function setExpiryDate(\DateTimeInterface $expiryDate): QuoteEntity
    {
        $this->expiryDate = $expiryDate;
        return $this;
    }
}
