<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Language\LanguageDefinition;
use Zeobv\CmsElements\Core\Content\ProductPage\Aggregate\ProductPageTranslation\ProductPageTranslationDefinition;

class LanguageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'ProductPageId',
               ProductPageTranslationDefinition::class,
                'mainTitle',
            )
        );

    }
    public function getDefinitionClass(): string
    {
        return LanguageDefinition::class;
    }
}
