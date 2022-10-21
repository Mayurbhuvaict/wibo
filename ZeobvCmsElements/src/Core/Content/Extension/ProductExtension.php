<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\Extension;

use Zeobv\CmsElements\Core\Content\ProductPage\ProductPageDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Content\Product\ProductDefinition;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToOneAssociationField(
                'pageProduct',
                'id',
                'product_id',
                ProductPageDefinition::class,
                false
            ))->addFlags(new ApiAware(), new CascadeDelete(), new Inherited())

        );

    }
    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
