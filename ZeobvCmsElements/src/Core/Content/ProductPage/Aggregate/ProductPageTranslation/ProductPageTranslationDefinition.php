<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\ProductPage\Aggregate\ProductPageTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Zeobv\CmsElements\Core\Content\ProductPage\ProductPageDefinition;

class ProductPageTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'product_page_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

//    public function getEntityClass(): string
//    {
//        return ProductPageTranslationEntity::class;
//    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): string
    {
        return ProductPageDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection(
            [
                (new StringField('main_title', 'mainTitle'))->addFlags(new Required()),
                (new StringField('main_description', 'mainDescription')),
                (new StringField('sub_title_one', 'subTitleOne')),
                (new StringField('sub_description_one', 'subDescriptionOne')),
                (new StringField('sub_title_two', 'subTitleTwo')),
                (new StringField('sub_description_two', 'subDescriptionTwo')),
                (new StringField('sub_title_three', 'subTitleThree')),
                (new StringField('sub_description_three', 'subDescriptionThree')),
                (new StringField('sub_title_four', 'subTitleFour')),
                (new StringField('sub_description_four', 'subDescriptionFour')),
                (new StringField('sub_title_five', 'subTitleFive')),
                (new StringField('sub_description_five', 'subDescriptionFive')),
                (new StringField('sub_title_six', 'subTitleSix')),
                (new StringField('sub_description_six', 'subDescriptionSix')),
                (new StringField('sub_title_seven', 'subTitleSeven')),
                (new StringField('sub_description_seven', 'subDescriptionSeven')),
                (new StringField('sub_title_eight', 'subTitleEight')),
                (new StringField('sub_description_eight', 'subDescriptionEight')),
            ]
        );
    }
}
