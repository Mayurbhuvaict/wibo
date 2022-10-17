<?php declare(strict_types=1);

namespace Zeobv\SchouwWitgoed;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Storefront\Framework\ThemeInterface;
use Zeobv\SchouwWitgoed\Migration\Step\PluginMigrationStep;

class ZeobvSchouwWitgoed extends Plugin implements ThemeInterface
{
    public function getThemeConfigPath(): string
    {
        return 'theme.json';
    }

    public function install(InstallContext $installContext): void
    {
        foreach ($installContext->getMigrationCollection()->getMigrationSteps() as $className => $migrationStep) {
            if (!$migrationStep instanceof PluginMigrationStep) {
                throw new \Exception($className . ' should be instance of ' . PluginMigrationStep::class);
            }
        }
    }

    public function update(UpdateContext $updateContext): void
    {
        foreach ($updateContext->getMigrationCollection()->getMigrationSteps() as $className => $migrationStep) {
            if (!$migrationStep instanceof PluginMigrationStep) {
                throw new \Exception($className . ' should be instance of ' . PluginMigrationStep::class);
            }
        }
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        $migrationCollection = $uninstallContext->getMigrationCollection();
        $connection = $this->container->get('Doctrine\DBAL\Connection');

        /** @var PluginMigrationStep $migrationStep */
        foreach (array_reverse($migrationCollection->getMigrationSteps()) as $migrationStep) {
            $migrationStep->down($connection, $uninstallContext->keepUserData());
        }
    }
}

