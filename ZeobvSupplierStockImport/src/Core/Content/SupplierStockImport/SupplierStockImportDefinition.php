<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Core\Content\SupplierStockImport;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
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
                (new IdField('id','id'))->addFlags(new ApiAware(), new Required(), new PrimaryKey()),
                (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new ApiAware(), new Required()),
                (new ReferenceVersionField(ProductDefinition::class))->addFlags(new ApiAware(), new Required()),
                (new JsonField('api_record','apiRecord'))->addFlags(new ApiAware(), new Required()),
                (new StringField('extra_field', 'extraField'))->addFlags(new ApiAware()),

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
