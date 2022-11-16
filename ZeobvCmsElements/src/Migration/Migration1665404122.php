<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
class Migration1665404122 extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1665404122;
    }

    public function update(Connection $connection): void
    {
        $zeoProductUpsellings = $connection->fetchOne(
            'SHOW COLUMNS FROM `' . ProductDefinition::ENTITY_NAME . '` WHERE `Field` LIKE :column;',
            ['column' => 'pageProduct']
        );
        if (!$zeoProductUpsellings) {
            $this->updateInheritance($connection, 'product', 'pageProduct');
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
