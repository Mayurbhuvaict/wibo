<?php declare(strict_types=1);

namespace Zeobv\SchouwWitgoed\Storefront\Twig;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Zeobv\SchouwWitgoed\Storefront\Service\MigrationAttributeService;

class MigrationAttributeExtension extends AbstractExtension
{
    private MigrationAttributeService $migrationAttributeService;

    public function __construct(MigrationAttributeService $migrationAttributeService)
    {
        $this->migrationAttributeService = $migrationAttributeService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getMigrationAttribute', [$this, 'getMigrationAttribute']),
        ];
    }

    /**
     * @param SalesChannelProductEntity $productEntity
     * @param string $migrationAttribute
     * @return mixed|null
     */
    public function getMigrationAttribute(SalesChannelProductEntity $productEntity, string $migrationAttribute)
    {
        return $this->migrationAttributeService->getMigrationAttributeFromFieldset($productEntity, $migrationAttribute);
    }
}
