<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\Media\Cms;

use Shopware\Core\Content\Media\Cms\AbstractDefaultMediaResolver;
use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

class ImageCaptionCmsElementResolver extends ImageCmsElementResolver{

    private AbstractDefaultMediaResolver $mediaResolver;

    public function __construct(AbstractDefaultMediaResolver $mediaResolver)
    {
        $this->mediaResolver = $mediaResolver;
    }

    public function getType(): string
    {
        return 'image-caption';
    }
}
