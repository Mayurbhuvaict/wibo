<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\ProductPage;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(ProductPageEntity $entity)
 * @method void                set(string $key, ProductPageEntity $entity)
 * @method ProductPageEntity[]    getIterator()
 * @method ProductPageEntity[]    getElements()
 * @method ProductPageEntity|null get(string $key)
 * @method ProductPageEntity|null first()
 * @method ProductPageEntity|null last()
 */
class ProductPageCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductPageEntity::class;
    }
}
