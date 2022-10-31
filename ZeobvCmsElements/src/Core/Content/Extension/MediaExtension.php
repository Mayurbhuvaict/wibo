<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\Extension;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Zeobv\CmsElements\Core\Content\ProductPage\ProductPageDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Extension;

class MediaExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'imageMedia',
                ProductPageDefinition::class,
                'id'
            ))->addFlags(new CascadeDelete(), new Extension())
        );
    }
    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}

