<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Core\Content\Extension;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Zeobv\SupplierStockImport\Core\Content\SupplierStockImport\SupplierStockImportDefinition;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'supplierStockImport',
                SupplierStockImportDefinition::class,
                'product_id'
            ))->addFlags(new SetNullOnDelete()),
        );

    }
    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
