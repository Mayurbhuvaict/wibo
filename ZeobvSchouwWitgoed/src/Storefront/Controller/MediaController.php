<?php declare(strict_types=1);

namespace Zeobv\SchouwWitgoed\Storefront\Controller;

use League\Flysystem\FilesystemInterface;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\Pathname\UrlGeneratorInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class MediaController extends StorefrontController
{
    private EntityRepositoryInterface $mediaRepository;
    private FilesystemInterface $publicFilesystem;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        EntityRepositoryInterface $mediaRepository,
        FilesystemInterface $publicFilesystem,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->mediaRepository = $mediaRepository;
        $this->publicFilesystem = $publicFilesystem;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/media/{fileName}.pdf", name="zeobv.frontend.media.pdf", methods={"GET"})
     */
    public function showPdf(Request $request, Context $context, string $fileName): Response
    {
        $media = $this->getMediaByFilename($fileName, $context);

        if (!$media || is_null($mediaPath = $this->getMediaPath($media))) {
            throw new NotFoundHttpException;
        }

        return new Response($this->publicFilesystem->read($mediaPath), 200, [
            'Content-type' => 'application/pdf',
        ]);
    }

    protected function getMediaByFilename(string $fileName, Context $context): ?MediaEntity
    {
        return $this->mediaRepository->search(
            (new Criteria)->addFilter(new EqualsFilter('fileName', $fileName)),
            $context
        )->getEntities()->first();
    }

    protected function getMediaPath(MediaEntity $media): ?string
    {
        $mediaPath = $this->urlGenerator->getRelativeMediaUrl($media);
        if (!$this->publicFilesystem->has($mediaPath)) {
            return null;
        }

        return $mediaPath;
    }
}
