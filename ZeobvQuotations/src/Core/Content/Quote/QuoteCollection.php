<?php


namespace Zeobv\Quotations\Core\Content\Quote;


use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void             add(QuoteEntity $entity)
 * @method void             set(string $key, QuoteEntity $entity)
 * @method QuoteEntity[]    getIterator()
 * @method QuoteEntity[]    getElements()
 * @method QuoteEntity|null get(string $key)
 * @method QuoteEntity|null first()
 * @method QuoteEntity|null last()
 */
class QuoteCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'zeobv_quote_collection';
    }

    protected function getExpectedClass(): string
    {
        return QuoteEntity::class;
    }
}
