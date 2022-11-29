<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Core\Content\SupplierStockImport;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class SupplierStockImportDefinition extends EntityDefinition
{
    public const ENTITY_NAME = "supplier_stock_import";

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return SupplierStockImportEntity::class;
    }

    public function getCollectionClass(): string
    {
        return SupplierStockImportCollection::class;
    }


    protected function defineFields(): FieldCollection
    {
        return new FieldCollection(
            [
                (new IdField('id', 'id'))->addFlags(new ApiAware(), new Required(), new PrimaryKey()),
                (new FkField(
                    'product_id',
                    'productId',
                    ProductDefinition::class
                ))->addFlags(new ApiAware(), new Required()),
                (new ReferenceVersionField(ProductDefinition::class))->addFlags(new ApiAware(), new Required()),
                (new StringField('ean_number', 'eanNumber'))->addFlags(new ApiAware()),
                (new LongTextField('atag_api_record', 'atagApiRecord'))->addFlags(new AllowHtml()),
                (new LongTextField('etna_api_record', 'etnaApiRecord'))->addFlags(new AllowHtml()),
                (new LongTextField('pelgrim_api_record', 'pelgrimApiRecord'))->addFlags(new AllowHtml()),
                (new LongTextField('hisense_api_record', 'hisenseApiRecord'))->addFlags(new AllowHtml()),
                (new LongTextField('asko_api_record', 'askoApiRecord'))->addFlags(new AllowHtml()),
                (new LongTextField('amacom_api_record', 'amacomApiRecord'))->addFlags(new AllowHtml()),
                (new LongTextField('boretti_api_record', 'borettiApiRecord'))->addFlags(new AllowHtml()),
                (new LongTextField('inventum_api_record', 'inventumApiRecord'))->addFlags(new AllowHtml()),
                (new LongTextField('smeg_api_record', 'smegApiRecord'))->addFlags(new AllowHtml()),
                (new DateTimeField('last_usage_at', 'lastUsageAt')),
                new ManyToOneAssociationField(
                    'product',
                    'product_id',
                    ProductDefinition::class,
                    'id',
                    false
                ),

            ]
        );
    }
}
