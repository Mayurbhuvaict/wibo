<?php


namespace Zeobv\Quotations\Core\Content\Order;


use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Extension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Zeobv\Quotations\Core\Content\Quote\QuoteDefinition;

class OrderExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToOneAssociationField(
                'zeobvQuote',
                'id',
                'order_id',
                QuoteDefinition::class,
                false
            ))->addFlags(new Extension)
        );
    }

    public function getDefinitionClass(): string
    {
        return OrderDefinition::class;
    }
}
