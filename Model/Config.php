<?php
declare(strict_types=1);


namespace Blackbird\CleanProductImageCache\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Blackbird\UrlRewrite\Model
 **/
class Config
{
    public const CONFIG_PATH_CLEAN_IMAGE_CACHE_ENABLE = 'blackbird_clean_image_cache/general/enable';
    public const CONFIG_PATH_CLEAN_IMAGE_CACHE_AREAS = 'blackbird_clean_image_cache/general/areas';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int|string|null|ScopeInterface $storeId
     * @return bool
     */
    public function isEnable($storeId = null): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_PATH_CLEAN_IMAGE_CACHE_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getAreas($storeId = null): array
    {
        return \explode(',', $this->scopeConfig->getValue(
            self::CONFIG_PATH_CLEAN_IMAGE_CACHE_AREAS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ?: '');
    }
}
