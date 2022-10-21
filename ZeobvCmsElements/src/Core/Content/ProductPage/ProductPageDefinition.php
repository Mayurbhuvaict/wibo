<?php
declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\ProductPage;


use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Zeobv\CmsElements\Core\Content\ProductPage\Aggregate\ProductPageTranslation\ProductPageTranslationDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;


class ProductPageDefinition extends EntityDefinition
{
    public const ENTITY_NAME = "product_page";

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

//    public function getEntityClass(): string
//    {
//        return ProductPageEntity::class;
//    }
//
//    public function getCollectionClass(): string
//    {
//        return ProductPageCollection::class;
//    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection(
            [
                (new IdField('id','id'))->addFlags(new ApiAware(), new Required(), new PrimaryKey()),
                (new TranslatedField('mainTitle'))->addFlags(new ApiAware()),
                (new TranslatedField('mainDescription'))->addFlags(new ApiAware()),
                (new TranslatedField('subTitleOne'))->addFlags(new ApiAware()),
                (new TranslatedField('subDescriptionOne'))->addFlags(new ApiAware()),
                (new TranslatedField('subTitleTwo'))->addFlags(new ApiAware()),
                (new TranslatedField('subDescriptionTwo'))->addFlags(new ApiAware()),
                (new TranslatedField('subTitleThree'))->addFlags(new ApiAware()),
                (new TranslatedField('subDescriptionThree'))->addFlags(new ApiAware()),
                (new TranslatedField('subTitleFour'))->addFlags(new ApiAware()),
                (new TranslatedField('subDescriptionFour'))->addFlags(new ApiAware()),
                (new TranslatedField('subTitleFive'))->addFlags(new ApiAware()),
                (new TranslatedField('subDescriptionFive'))->addFlags(new ApiAware()),
                (new TranslatedField('subTitleSix'))->addFlags(new ApiAware()),
                (new TranslatedField('subDescriptionSix'))->addFlags(new ApiAware()),
                (new TranslatedField('subTitleSeven'))->addFlags(new ApiAware()),
                (new TranslatedField('subDescriptionSeven'))->addFlags(new ApiAware()),
                (new TranslatedField('subTitleEight'))->addFlags(new ApiAware()),
                (new TranslatedField('subDescriptionEight'))->addFlags(new ApiAware()),

                //Foreign key Field
                new FkField('media_id', 'mediaId', MediaDefinition::class),
                (new FkField('product_id','productId',ProductDefinition::class))->addFlags(new ApiAware()),
                (new ReferenceVersionField(ProductDefinition::class))->addFlags(new ApiAware(), new Inherited()),

                //Translation Association field
                (new TranslationsAssociationField(ProductPageTranslationDefinition::class,'product_page_id'))->addFlags(new ApiAware(),new Required()),

                //ManyToOne Association
                new ManyToOneAssociationField(
                    'media',
                    'media_id',
                    MediaDefinition::class,
                    'id',
                    false
                ),

                //OneToOne Association for Foreign Key Field
                new OneToOneAssociationField(
                    'productDetail',
                    'product_id',
                    'id',
                    ProductDefinition::class,
                    true),
            ]
        );

    }
}

