<?php declare(strict_types=1);

namespace Zeobv\SchouwWitgoed\Storefront\Framework\Seo\SeoUrlRoute;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class PdfSeoUrlRoute implements SeoUrlRouteInterface
{
    public const ROUTE_NAME = 'zeobv.frontend.media.pdf';
    public const DEFAULT_TEMPLATE = 'media/{{ media.fileName }}.pdf';

    private EntityDefinition $mediaDefinition;

    public function __construct(
        EntityDefinition $mediaDefinition
    )
    {
        $this->mediaDefinition = $mediaDefinition;
    }

    public function getConfig(): SeoUrlRouteConfig
    {
        return new SeoUrlRouteConfig(
            $this->mediaDefinition,
            self::ROUTE_NAME,
            self::DEFAULT_TEMPLATE,
            true
        );
    }

    public function prepareCriteria(Criteria $criteria): void
    {
        // Only generate SEO URLs for PDFs
        $criteria->addFilter(new EqualsFilter('fileExtension', 'pdf'));
    }

    public function getMapping(Entity $entity, ?SalesChannelEntity $salesChannel): SeoUrlMapping
    {
        if (!$entity instanceof MediaEntity) {
            throw new \InvalidArgumentException('Expected MediaEntity');
        }

        $mediaJson = $entity->jsonSerialize();

        return new SeoUrlMapping(
            $entity,
            [
                'fileName' => $entity->getFileName(),
            ],
            [
                'media' => $mediaJson,
            ]
        );
    }
}
