<?php

declare(strict_types=1);

namespace Zeobv\CmsElements\Installer;

use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

interface InstallerInterface
{
    public function install(InstallContext $context): void;

    public function uninstall(UninstallContext $context): void;

}
