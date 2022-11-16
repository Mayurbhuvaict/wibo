<?php declare(strict_types=1);

namespace Zeobv\Quotations\Service;

use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * Class ConfigService
 *
 * @package Zeobv\Quotations\Service
 */
class ConfigService {

    /**
     * @var SystemConfigService
     */
    protected $configService;

    /**
     * @var string
     */
    protected $configPrefix;

    /**
     * ConfigService constructor.
     *
     * @param SystemConfigService $systemConfigService
     * @param string              $pluginName
     */
    public function __construct(
        SystemConfigService $systemConfigService,
        string $pluginName
    )
    {
        $this->configService = $systemConfigService;
        $this->configPrefix = "$pluginName.config";
    }

    /**
     * @param SalesChannelEntity $salesChannel
     * @param bool               $default
     *
     * @return bool
     */
    public function getIsActive(SalesChannelEntity $salesChannel = null, $default = false): bool
    {
        $scId = $salesChannel ? $salesChannel->getId() : null;
        $active = $this->configService->get("$this->configPrefix.isActive", $scId);

        return !is_null($active) ? $active : $default;
    }

    /**
     * @param string $method
     * @param mixed  ...$args
     *
     * @return array|mixed|null
     */
    public function __call(string $method, array $args = [])
    {
        # Check if required input is provided for method
        $this->typeCheckArgs($method, $args);

        # Get method params
        $salesChannel = isset($args[0]) ? $args[0] : null;
        $default = isset($args[1]) ? $args[1] : null;

        # Get config name
        $configName = lcfirst(substr($method, 3));

        $scId = $salesChannel ? $salesChannel->getId() : null;
        $config = $this->configService->get($this->configPrefix .'.'. $configName, $scId);

        return $config ?? $default;
    }

    /**
     * @param $method
     * @param $args
     */
    private function typeCheckArgs($method, array $args)
    {
        if (isset($args[0]) && $args[0] !== null && !$args[0] instanceof SalesChannelEntity) {
            $argType = get_class($args[0]);
            $method = __CLASS__."::$method()";
            throw new \TypeError("Argument 1 passed to $method must be an instance of Shopware\Core\System\SalesChannel\SalesChannelEntity, instance of $argType given");
        }
    }
}
