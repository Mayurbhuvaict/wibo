<?php


namespace Zeobv\Quotations\Core\Content\Quote;


use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StateMachineStateField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\NumberRange\DataAbstractionLayer\NumberRangeField;
use Shopware\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateDefinition;
use Zeobv\Quotations\Core\StateMachine\QuotationStates;

class QuoteDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'zeobv_quote';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return QuoteCollection::class;
    }

    public function getEntityClass(): string
    {
        return QuoteEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),

            (new NumberRangeField('quote_number', 'quoteNumber'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING, false), new Required()),

            (new FkField('order_id', 'orderId', OrderDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new ReferenceVersionField(OrderDefinition::class, 'order_version_id'))->addFlags(new ApiAware(), new Required()),

            (new StateMachineStateField('quotation_state_id', 'quotationStateId', QuotationStates::STATE_MACHINE))->addFlags(new Required()),
            (new ManyToOneAssociationField('stateMachineState', 'quotation_state_id', StateMachineStateDefinition::class, 'id', true))
                ->addFlags(new ApiAware()),

            (new DateField('expiry_date', 'expiryData'))->addFlags(new ApiAware()),

            (new OneToOneAssociationField('order', 'order_id', 'id', OrderDefinition::class, false))->addFlags(new ApiAware(), new CascadeDelete()),
        ]);
    }
}
