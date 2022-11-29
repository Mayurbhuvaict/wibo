<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Core\Content\SupplierStockImport;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(SupplierStockImportEntity $entity)
 * @method void                set(string $key, SupplierStockImportEntity $entity)
 * @method SupplierStockImportEntity[]    getIterator()
 * @method SupplierStockImportEntity[]    getElements()
 * @method SupplierStockImportEntity|null get(string $key)
 * @method SupplierStockImportEntity|null first()
 * @method SupplierStockImportEntity|null last()
 */
class SupplierStockImportCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return SupplierStockImportEntity::class;
    }
}