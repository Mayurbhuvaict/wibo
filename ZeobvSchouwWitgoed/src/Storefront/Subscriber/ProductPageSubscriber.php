<?php

namespace Zeobv\SchouwWitgoed\Storefront\Subscriber;

use Shopware\Core\Content\Media\Aggregate\MediaFolder\MediaFolderEntity;
use Shopware\Core\Content\Media\Api\MediaUploadController;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\Api\Response\ResponseFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Zeobv\SchouwWitgoed\Migration\Step\PluginMigrationStep;
use Zeobv\SchouwWitgoed\Storefront\Service\CustomFieldsService;
use Zeobv\SchouwWitgoed\Storefront\Service\MigrationAttributeService;

class ProductPageSubscriber implements EventSubscriberInterface
{
    private CustomFieldsService $customFieldsService;
    private EntityRepositoryInterface $mediaRepository;
    private EntityRepositoryInterface $mediaFolderRepository;
    private MigrationAttributeService $migrationAttributeService;
    private EntityRepositoryInterface $productRepository;
    private MediaUploadController $mediaUploadController;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        CustomFieldsService $customFieldsService,
        EntityRepositoryInterface $mediaRepository,
        EntityRepositoryInterface $mediaFolderRepository,
        EntityRepositoryInterface $productRepository,
        MigrationAttributeService $migrationAttributeService,
        MediaUploadController $mediaUploadController,
        ResponseFactoryInterface $responseFactory
    )
    {
        $this->customFieldsService = $customFieldsService;
        $this->mediaRepository = $mediaRepository;
        $this->mediaFolderRepository = $mediaFolderRepository;
        $this->productRepository = $productRepository;
        $this->migrationAttributeService = $migrationAttributeService;
        $this->mediaUploadController = $mediaUploadController;
        $this->responseFactory = $responseFactory;
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
            ProductEvents::PRODUCT_LOADED_EVENT => 'onProductsLoaded',
            ProductEvents::PRODUCT_WRITTEN_EVENT => 'onProductsWritten',
        ];
    }

    /**
     * Set canonical id of parent and make custom field values available.
     *
     * @param ProductPageLoadedEvent $event
     */
    public function onProductPageLoaded(ProductPageLoadedEvent $event)
    {
        $product = $event->getPage()->getProduct();

        if ($product->getParentId()) {
            $product->setCanonicalProductId($product->getParentId());
        }

        $allSelectCustomFields = $this->customFieldsService->getCustomFieldsFromSelectType($event->getContext(), $event->getRequest()->getLocale(), $event->getRequest()->getDefaultLocale());
        $this->customFieldsService->convertProductsCustomFields([$product], $allSelectCustomFields);

        $this->convertDownloadsCustomFields($product, $event->getContext());
    }

    /**
     * Adds the showPrice value to the ZeobvSchouwWitgoed extension.
     *
     * @param EntityLoadedEvent $event
     */
    public function onProductsLoaded(EntityLoadedEvent $event): void
    {
        $attribute = 'bijzonderheden_346';

        foreach ($event->getEntities() as $productEntity) {
            $attributeValue = $this->migrationAttributeService->getMigrationAttributeFromFieldset($productEntity, $attribute);

            $showPrice =
                (strtolower($attributeValue) !== 'option_4362' && strtolower($attributeValue) !== 'prijs op aanvraag') &&
                (strtolower($attributeValue) !== 'option_4360' && strtolower($attributeValue) !== 'mag alleen in winkel verkocht worden');

            $productEntity->addExtension('ZeobvSchouwWitgoed', new ArrayStruct([
                'showPrice' => $showPrice
            ]));
        }
    }

    public function onProductsWritten(EntityWrittenEvent $event)
    {
        /** @var ProductCollection $productEntities */
        $productEntities = $this->productRepository->search(new Criteria($event->getIds()), $event->getContext())->getEntities();
        $downloadsCustomFieldNamePrefix = PluginMigrationStep::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME . '_url_';

        $productWriteData = [];
        foreach ($productEntities as $productEntity) {
            $productCustomFields = $productEntity->getCustomFields() ?: [];
            $downloadUrls = [];

            foreach ($productCustomFields as $customFieldName => $customFieldValue) {
                // If custom field name starts right prefix
                if (substr($customFieldName, 0, strlen($downloadsCustomFieldNamePrefix)) === $downloadsCustomFieldNamePrefix) {
                    $downloadUrls[$customFieldName] = $customFieldValue;
                }
            }

            if (empty($downloadUrls)) {
                continue;
            }

            $this->downloadMediaForProduct($productEntity, $downloadUrls, $event->getContext(), $productWriteData);
        }

        $this->productRepository->update(array_values($productWriteData), $event->getContext());
    }

    protected function convertDownloadsCustomFields(SalesChannelProductEntity $product, Context $context): void
    {
        $customFields = $product->getCustomFields();
        $downloads = array_filter($customFields, function($key) {
            return strpos($key, PluginMigrationStep::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME.'_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        if (empty($downloads)) {
            return;
        }

        $media = $this->mediaRepository->search(
            new Criteria(
                array_values($downloads)
            ),
            $context
        )->getEntities();

        foreach ($downloads as $key => $mediaId) {
            unset($customFields[$key]);
        }
        $customFields['zeobv_schouw_witgoed_custom_product_downloads'] = $media;

        $product->setCustomFields($customFields);
    }

    protected function downloadMediaForProduct(ProductEntity $product, array $downloadUrls, Context $context, array &$productWriteData): void
    {
        try {
            foreach ($downloadUrls as $downloadUrlCustomFieldName => $downloadUrl) {
                if (!is_string($downloadUrl) || empty($downloadUrl)) {
                    continue;
                }

                $media = $this->downloadMedia(
                    $downloadUrl,
                    sprintf('%s %s', $product->getName(), $this->getFilenameSuffixForCustomFieldName($downloadUrlCustomFieldName)),
                    $context
                );

                $downloadMediaIdCustomFieldName = str_replace('_url', '', $downloadUrlCustomFieldName);
                $productWriteData[$product->getId()]['id'] = $product->getId();
                $productWriteData[$product->getId()]['customFields'][$downloadUrlCustomFieldName] = null;
                $productWriteData[$product->getId()]['customFields'][$downloadMediaIdCustomFieldName] = $media->getId();
            }
        } catch (\Throwable $exception) {
            dd($exception);
        }
    }

    private function downloadMedia(string $downloadUrl, string $fileName, Context $context): ?MediaEntity
    {
        $productMediaFolder = $this->getProductMediaFolder($context);

        $this->mediaRepository->upsert([[
            'id' => $mediaId = md5($downloadUrl),
            'mediaFolderId' => $productMediaFolder ? $productMediaFolder->getId() : null,
        ]], $context);

        $fileExtension = pathinfo($downloadUrl)['extension'];

        $uploadFileRequest = new Request([
            'extension' => $fileExtension,
            'fileName' => $fileName,
        ], [], [], [], [], [
            'HTTP_CONTENT_LENGTH' => 0,
            'HTTP_CONTENT_TYPE' => 'application/pdf',
        ], '');

        $this->mediaUploadController->upload(
            $uploadFileRequest,
            $mediaId,
            $context,
            $this->responseFactory
        );

        return $this->mediaRepository->search(new Criteria([$mediaId]), $context)->first();
    }

    private function getFilenameSuffixForCustomFieldName(string $customFieldName): string
    {
        $suffixes = [
            PluginMigrationStep::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME . '_url_1' => 'Download 1',
            PluginMigrationStep::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME . '_url_2' => 'Download 2',
            PluginMigrationStep::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME . '_url_3' => 'Download 3',
            PluginMigrationStep::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME . '_url_4' => 'Download 4',
            PluginMigrationStep::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME . '_url_5' => 'Download 5',
        ];

        if (array_key_exists($customFieldName, $suffixes)) {
            return $suffixes[$customFieldName];
        }

        return '';
    }

    protected function getProductMediaFolder(Context $context): ?MediaFolderEntity
    {
        return $this->mediaFolderRepository->search(
            (new Criteria)->addFilter(new EqualsFilter('name', 'Product Media')),
            $context
        )->first();
    }
}
