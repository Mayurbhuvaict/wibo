<?php declare(strict_types=1);

namespace Zeobv\SchouwWitgoed\Core\Content\Media\Thumbnail;

use League\Flysystem\FilesystemInterface;
use setasign\Fpdi\PdfParser\Type\PdfType;
use Shopware\Core\Content\Media\Aggregate\MediaFolder\MediaFolderEntity;
use Shopware\Core\Content\Media\Aggregate\MediaFolderConfiguration\MediaFolderConfigurationEntity;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailCollection;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnailSize\MediaThumbnailSizeCollection;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnailSize\MediaThumbnailSizeEntity;
use Shopware\Core\Content\Media\Exception\FileTypeNotSupportedException;
use Shopware\Core\Content\Media\Exception\ThumbnailCouldNotBeSavedException;
use Shopware\Core\Content\Media\MediaCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\MediaType\DocumentType;
use Shopware\Core\Content\Media\MediaType\ImageType;
use Shopware\Core\Content\Media\MediaType\MediaType;
use Shopware\Core\Content\Media\Pathname\UrlGeneratorInterface;
use Shopware\Core\Content\Media\Thumbnail\ThumbnailService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

// Unfortunately the decorated service has to be extended because the original service doesn't have an interface
class PdfThumbnailService extends ThumbnailService
{
    public const PDF_THUMBNAIL_FORMAT = 'jpg';

    private EntityRepositoryInterface $mediaThumbnailRepository;

    private FilesystemInterface $filesystemPublic;

    private FilesystemInterface $filesystemPrivate;

    private UrlGeneratorInterface $urlGenerator;

    private EntityRepositoryInterface $mediaFolderRepository;

    /** @var ThumbnailService $thumbnailService */
    private $thumbnailService;

    /**
     * @param ThumbnailService $thumbnailService
     * @param EntityRepositoryInterface $mediaThumbnailRepository
     * @param FilesystemInterface $fileSystemPublic
     * @param FilesystemInterface $fileSystemPrivate
     * @param UrlGeneratorInterface $urlGenerator
     * @param EntityRepositoryInterface $mediaFolderRepository
     */
    public function __construct(
        $thumbnailService, // This variable is not recommended to be type checked because it does not have an interface
        EntityRepositoryInterface $mediaThumbnailRepository,
        FilesystemInterface $fileSystemPublic,
        FilesystemInterface $fileSystemPrivate,
        UrlGeneratorInterface $urlGenerator,
        EntityRepositoryInterface $mediaFolderRepository
    ) {
        $this->thumbnailService = $thumbnailService;
        $this->mediaThumbnailRepository = $mediaThumbnailRepository;
        $this->filesystemPublic = $fileSystemPublic;
        $this->filesystemPrivate = $fileSystemPrivate;
        $this->urlGenerator = $urlGenerator;
        $this->mediaFolderRepository = $mediaFolderRepository;

        // Extract the decorated service from args to parse all remaining args to parent constructor
        $args = func_get_args();
        array_splice($args, 0, 1);
        parent::__construct(...$args);
    }

    public function generate(MediaCollection $collection, Context $context): int
    {
        $generatedImageThumbnailsCount = $this->thumbnailService->generate($collection, $context);

        $pdfsToGenerate = [];

        // Check if the media is a pdf and if thumbnails can be generated
        foreach ($collection as $media) {
            if ($media->getThumbnails() === null) {
                throw new \RuntimeException('Thumbnail association not loaded - please pre load media thumbnails');
            }

            if (!$this->mediaIsPdf($media, $context)) {
                continue;
            }

            $mediaFolder = $media->getMediaFolder();
            if ($mediaFolder === null) {
                continue;
            }

            if ($mediaFolder->getConfiguration() === null) {
                continue;
            }

            $pdfsToGenerate[] = $media;
        }

        $thumbnailEntitiesData = [];
        foreach ($pdfsToGenerate as $media) {
            $config = $media->getMediaFolder()->getConfiguration();

            $thumbnails = $this->createThumbnailsForSizes($media, $config, $config->getMediaThumbnailSizes());

            foreach ($thumbnails as $thumbnail) {
                $thumbnailEntitiesData[] = $thumbnail;
            }
        }

        $thumbnailEntitiesData = array_values(array_filter($thumbnailEntitiesData));

        if (empty($thumbnailEntitiesData)) {
            return $generatedImageThumbnailsCount;
        }

        $context->scope(Context::SYSTEM_SCOPE, function ($context) use ($thumbnailEntitiesData): void {
            $this->mediaThumbnailRepository->create($thumbnailEntitiesData, $context);
        });

        return \count($thumbnailEntitiesData) + $generatedImageThumbnailsCount;
    }

    /**
     * @deprecated tag:v6.5.0 - Use `generate` instead
     *
     * @throws FileTypeNotSupportedException
     * @throws ThumbnailCouldNotBeSavedException
     */
    public function generateThumbnails(MediaEntity $media, Context $context): int
    {
        if (!$this->mediaCanHaveThumbnails($media, $context)) {
            $this->deleteAssociatedThumbnails($media, $context);

            return 0;
        }

        $mediaFolder = $media->getMediaFolder();
        if ($mediaFolder === null) {
            return 0;
        }

        $config = $mediaFolder->getConfiguration();
        if ($config === null) {
            return 0;
        }

        /** @var MediaThumbnailCollection $toBeDeletedThumbnails */
        $toBeDeletedThumbnails = $media->getThumbnails();
        $this->mediaThumbnailRepository->delete($toBeDeletedThumbnails->getIds(), $context);

        $update = $this->createThumbnailsForSizes($media, $config, $config->getMediaThumbnailSizes());

        if (empty($update)) {
            return 0;
        }

        $context->scope(Context::SYSTEM_SCOPE, function ($context) use ($update): void {
            $this->mediaThumbnailRepository->create($update, $context);
        });

        return \count($update);
    }

    /**
     * @throws FileTypeNotSupportedException
     * @throws ThumbnailCouldNotBeSavedException
     *
     * @deprecated tag:v6.5.0 - Parameter $strict will be mandatory in future implementation
     */
    public function updateThumbnails(MediaEntity $media, Context $context /* , bool $strict = false */): int
    {
        if (!$this->mediaCanHaveThumbnails($media, $context)) {
            $this->deleteAssociatedThumbnails($media, $context);

            return 0;
        }

        $mediaFolder = $media->getMediaFolder();
        if ($mediaFolder === null) {
            return 0;
        }

        $config = $mediaFolder->getConfiguration();
        if ($config === null) {
            return 0;
        }

        $strict = \func_get_args()[2] ?? false;

        $toBeCreatedSizes = new MediaThumbnailSizeCollection($config->getMediaThumbnailSizes()->getElements());
        $toBeDeletedThumbnails = new MediaThumbnailCollection($media->getThumbnails()->getElements());

        foreach ($toBeCreatedSizes as $thumbnailSize) {
            foreach ($toBeDeletedThumbnails as $thumbnail) {
                if (!$this->isSameDimension($thumbnail, $thumbnailSize)) {
                    continue;
                }

                if ($strict === true
                    && !$this->getFileSystem($media)->has($this->urlGenerator->getRelativeThumbnailUrl($media, $thumbnail))) {
                    continue;
                }

                $toBeDeletedThumbnails->remove($thumbnail->getId());
                $toBeCreatedSizes->remove($thumbnailSize->getId());

                continue 2;
            }
        }

        $this->mediaThumbnailRepository->delete($toBeDeletedThumbnails->getIds(), $context);

        $update = $this->createThumbnailsForSizes($media, $config, $toBeCreatedSizes);

        if (empty($update)) {
            return 0;
        }

        $context->scope(Context::SYSTEM_SCOPE, function ($context) use ($update): void {
            $this->mediaThumbnailRepository->create($update, $context);
        });

        return \count($update);
    }

    public function deleteThumbnails(MediaEntity $media, Context $context): void
    {
        $this->deleteAssociatedThumbnails($media, $context);
    }

    /**
     * @throws FileTypeNotSupportedException
     * @throws ThumbnailCouldNotBeSavedException
     */
    private function createThumbnailsForSizes(
        MediaEntity $media,
        MediaFolderConfigurationEntity $config,
        ?MediaThumbnailSizeCollection $thumbnailSizes
    ): array {
        if ($thumbnailSizes === null || $thumbnailSizes->count() === 0) {
            return [];
        }

        $mediaImage = $this->getImageResourceForPdf($media);
        $originalImageSize = $this->getOriginalImageSize($mediaImage);

        $savedThumbnails = [];

        try {
            foreach ($thumbnailSizes as $size) {
                $thumbnailSize = $this->calculateThumbnailSize($originalImageSize, $size, $config);
                $thumbnail = $this->createNewImage(
                    $mediaImage,
                    $originalImageSize,
                    $thumbnailSize
                );

                $url = $this->urlGenerator->getRelativeThumbnailUrl(
                    $media,
                    (new MediaThumbnailEntity())->assign(['width' => $size->getWidth(), 'height' => $size->getHeight()])
                );

                $this->writeThumbnail($thumbnail, $media, $url, $config->getThumbnailQuality());

                $savedThumbnails[] = [
                    'mediaId' => $media->getId(),
                    'width' => $size->getWidth(),
                    'height' => $size->getHeight(),
                ];

                imagedestroy($thumbnail);
            }
            imagedestroy($mediaImage);
        } finally {
            return $savedThumbnails;
        }
    }

    private function ensureConfigIsLoaded(MediaEntity $media, Context $context): void
    {
        $mediaFolderId = $media->getMediaFolderId();
        if ($mediaFolderId === null) {
            return;
        }

        if ($media->getMediaFolder() !== null) {
            return;
        }

        $criteria = new Criteria([$mediaFolderId]);
        $criteria->addAssociation('configuration.mediaThumbnailSizes');

        /** @var MediaFolderEntity $folder */
        $folder = $this->mediaFolderRepository->search($criteria, $context)->get($mediaFolderId);
        $media->setMediaFolder($folder);
    }

    /**
     * @throws FileTypeNotSupportedException
     *
     * @return resource
     */
    private function getImageResourceForPdf(MediaEntity $media)
    {
        $pdfFilePath = '/app/public/' . $this->urlGenerator->getRelativeMediaUrl($media);

        // [0] stands for first page of PDF
        $image = new \imagick($pdfFilePath . '[0]');
        $image->setImageFormat(self::PDF_THUMBNAIL_FORMAT);
        $file = (string) $image;
        $image = @imagecreatefromstring($file);

        if (\function_exists('exif_read_data')) {
            /** @var resource $stream */
            $stream = fopen('php://memory', 'r+b');

            try {
                // use in-memory stream to read the EXIF-metadata,
                // to avoid downloading the image twice from a remote filesystem
                fwrite($stream, $file);
                rewind($stream);

                $exif = @exif_read_data($stream);

                if ($exif !== false) {
                    if (!empty($exif['Orientation']) && $exif['Orientation'] === 8) {
                        $image = imagerotate($image, 90, 0);
                    } elseif (!empty($exif['Orientation']) && $exif['Orientation'] === 3) {
                        $image = imagerotate($image, 180, 0);
                    } elseif (!empty($exif['Orientation']) && $exif['Orientation'] === 6) {
                        $image = imagerotate($image, -90, 0);
                    }
                }
            } catch (\Exception $e) {
                // Ignore.
            } finally {
                fclose($stream);
            }
        }

        return $image;
    }

    /**
     * @param resource $image
     */
    private function getOriginalImageSize($image): array
    {
        return [
            'width' => imagesx($image),
            'height' => imagesy($image),
        ];
    }

    /**
     * @param array $imageSize
     * @param MediaThumbnailSizeEntity $preferredThumbnailSize
     * @param MediaFolderConfigurationEntity $config
     *
     * @return array{width: int, height: int}
     */
    private function calculateThumbnailSize(
        array $imageSize,
        MediaThumbnailSizeEntity $preferredThumbnailSize,
        MediaFolderConfigurationEntity $config
    ): array {
        if (!$config->getKeepAspectRatio() || $preferredThumbnailSize->getWidth() !== $preferredThumbnailSize->getHeight()) {
            $calculatedWidth = $preferredThumbnailSize->getWidth();
            $calculatedHeight = $preferredThumbnailSize->getHeight();

            $useOriginalSizeInThumbnails = $imageSize['width'] < $calculatedWidth || $imageSize['height'] < $calculatedHeight;

            return $useOriginalSizeInThumbnails ? [
                'width' => $imageSize['width'],
                'height' => $imageSize['height'],
            ] : [
                'width' => $calculatedWidth,
                'height' => $calculatedHeight,
            ];
        }

        if ($imageSize['width'] >= $imageSize['height']) {
            $aspectRatio = $imageSize['height'] / $imageSize['width'];

            $calculatedWidth = $preferredThumbnailSize->getWidth();
            $calculatedHeight = (int) ceil($preferredThumbnailSize->getHeight() * $aspectRatio);

            $useOriginalSizeInThumbnails = $imageSize['width'] < $calculatedWidth || $imageSize['height'] < $calculatedHeight;

            return $useOriginalSizeInThumbnails ? [
                'width' => $imageSize['width'],
                'height' => $imageSize['height'],
            ] : [
                'width' => $calculatedWidth,
                'height' => $calculatedHeight,
            ];
        }

        $aspectRatio = $imageSize['width'] / $imageSize['height'];

        $calculatedWidth = (int) ceil($preferredThumbnailSize->getWidth() * $aspectRatio);
        $calculatedHeight = $preferredThumbnailSize->getHeight();

        $useOriginalSizeInThumbnails = $imageSize['width'] < $calculatedWidth || $imageSize['height'] < $calculatedHeight;

        return $useOriginalSizeInThumbnails ? [
            'width' => $imageSize['width'],
            'height' => $imageSize['height'],
        ] : [
            'width' => $calculatedWidth,
            'height' => $calculatedHeight,
        ];
    }

    /**
     * @param resource $mediaImage
     *
     * @return resource
     */
    private function createNewImage($mediaImage, array $originalImageSize, array $thumbnailSize)
    {
        $thumbnail = imagecreatetruecolor($thumbnailSize['width'], $thumbnailSize['height']);

        $colorWhite = (int) imagecolorallocate($thumbnail, 255, 255, 255);
        imagefill($thumbnail, 0, 0, $colorWhite);

        imagesavealpha($thumbnail, true);
        imagecopyresampled(
            $thumbnail,
            $mediaImage,
            0,
            0,
            0,
            0,
            $thumbnailSize['width'],
            $thumbnailSize['height'],
            $originalImageSize['width'],
            $originalImageSize['height']
        );

        return $thumbnail;
    }

    /**
     * @param resource $thumbnail
     *
     * @throws ThumbnailCouldNotBeSavedException
     */
    private function writeThumbnail($thumbnail, MediaEntity $media, string $url, int $quality): void
    {
        ob_start();
        switch (self::PDF_THUMBNAIL_FORMAT) {
            case 'png':
                imagepng($thumbnail);

                break;
            case 'gif':
                imagegif($thumbnail);

                break;
            case 'jpg':
            case 'jpeg':
                imagejpeg($thumbnail, null, $quality);

                break;
            case 'webp':
                if (!\function_exists('imagewebp')) {
                    throw new ThumbnailCouldNotBeSavedException($url);
                }

                imagewebp($thumbnail, null, $quality);

                break;
        }
        $imageFile = ob_get_contents();
        ob_end_clean();

        if ($this->getFileSystem($media)->put($url, $imageFile) === false) {
            throw new ThumbnailCouldNotBeSavedException($url);
        }
    }

    private function mediaCanHaveThumbnails(MediaEntity $media, Context $context): bool
    {
        if (!$media->hasFile()) {
            return false;
        }

        if (!$this->thumbnailsAreGeneratable($media)) {
            return false;
        }

        $this->ensureConfigIsLoaded($media, $context);

        if ($media->getMediaFolder() === null || $media->getMediaFolder()->getConfiguration() === null) {
            return false;
        }

        return $media->getMediaFolder()->getConfiguration()->getCreateThumbnails();
    }

    private function thumbnailsAreGeneratable(MediaEntity $media): bool
    {
        return $media->getMediaType() instanceof ImageType
            && !$media->getMediaType()->is(ImageType::VECTOR_GRAPHIC)
            && !$media->getMediaType()->is(ImageType::ANIMATED)
            && !$media->getMediaType()->is(ImageType::ICON);
    }

    private function deleteAssociatedThumbnails(MediaEntity $media, Context $context): void
    {
        $associatedThumbnails = $media->getThumbnails()->getIds();
        $this->mediaThumbnailRepository->delete($associatedThumbnails, $context);
    }

    private function getFileSystem(MediaEntity $media): FilesystemInterface
    {
        if ($media->isPrivate()) {
            return $this->filesystemPrivate;
        }

        return $this->filesystemPublic;
    }

    private function isSameDimension(MediaThumbnailEntity $thumbnail, MediaThumbnailSizeEntity $thumbnailSize): bool
    {
        return $thumbnail->getWidth() === $thumbnailSize->getWidth()
            && $thumbnail->getHeight() === $thumbnailSize->getHeight();
    }

    protected function mediaIsPdf(MediaEntity $media, Context $context): bool
    {
        return $media->getMediaType() instanceof PdfType || $media->getMediaType() instanceof DocumentType;
    }
}
