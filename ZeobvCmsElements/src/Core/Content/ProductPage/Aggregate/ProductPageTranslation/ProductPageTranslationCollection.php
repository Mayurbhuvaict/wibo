<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\ProductPage\Aggregate\ProductPageTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(ProductPageTranslationEntity $entity)
 * @method void                set(string $key, ProductPageTranslationEntity $entity)
 * @method ProductPageTranslationEntity[]    getIterator()
 * @method ProductPageTranslationEntity[]    getElements()
 * @method ProductPageTranslationEntity|null get(string $key)
 * @method ProductPageTranslationEntity|null first()
 * @method ProductPageTranslationEntity|null last()
 */
class ProductPageTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductPageTranslationEntity::class;
    }
}
