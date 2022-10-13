<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\Media\Cms;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

class ImageCaptionCmsElementResolver extends ImageCmsElementResolver{

    public function getType(): string
    {
        return 'image-caption';
    }
}
