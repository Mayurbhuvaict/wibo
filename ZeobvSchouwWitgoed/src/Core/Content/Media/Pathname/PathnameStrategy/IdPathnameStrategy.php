<?php

namespace Zeobv\SchouwWitgoed\Core\Content\Media\Pathname\PathnameStrategy;

use Shopware\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\Pathname\PathnameStrategy\PathnameStrategyInterface;
use Zeobv\SchouwWitgoed\Core\Content\Media\Thumbnail\PdfThumbnailService;

class IdPathnameStrategy implements PathnameStrategyInterface
{
    private PathnameStrategyInterface $decoratedService;

    public function __construct(
        PathnameStrategyInterface $decoratedService
    )
    {
        $this->decoratedService = $decoratedService;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->decoratedService->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function generatePathHash(MediaEntity $media, ?MediaThumbnailEntity $thumbnail = null): ?string
    {
        return $this->decoratedService->generatePathHash(...func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function generatePhysicalFilename(MediaEntity $media, ?MediaThumbnailEntity $thumbnail = null): string
    {
        if (is_null($thumbnail) || $media->getFileExtension() !== 'pdf') {
            return $this->decoratedService->generatePhysicalFilename(...func_get_args());
        }

        // If generating filename for pdf thumbnail, make sure that thumbnail file extension is used instead of .pdf
        $media->setFileExtension(PdfThumbnailService::PDF_THUMBNAIL_FORMAT);
        $return = $this->decoratedService->generatePhysicalFilename(...func_get_args());
        $media->setFileExtension('pdf');

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePathCacheBuster(MediaEntity $media, ?MediaThumbnailEntity $thumbnail = null): ?string
    {
        return $this->decoratedService->generatePathCacheBuster(...func_get_args());
    }
}
