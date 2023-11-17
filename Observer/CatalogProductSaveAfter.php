<?php
declare(strict_types=1);


namespace Blackbird\CleanProductImageCache\Observer;

use Blackbird\CleanProductImageCache\Model\Config as CleanProductImageCacheConfig;
use Blackbird\CleanProductImageCache\Model\Service\CleanCacheStrategyPool;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CatalogProductSaveAfter
 * @package Blackbird\CleanProductImageCache\Observer
 **/
class CatalogProductSaveAfter implements ObserverInterface
{
    /**
     * @var \Blackbird\CleanProductImageCache\Model\Service\CleanCacheStrategyPool
     */
    protected CleanCacheStrategyPool $cacheStrategyPool;

    /**
     * @var \Blackbird\CleanProductImageCache\Model\Config
     */
    protected CleanProductImageCacheConfig $CleanProductImageCacheConfig;

    /**
     * @param \Blackbird\CleanProductImageCache\Model\Service\CleanCacheStrategyPool $cacheStrategyPool
     * @param \Blackbird\CleanProductImageCache\Model\Config $CleanProductImageCacheConfig
     */
    public function __construct(
        CleanCacheStrategyPool $cacheStrategyPool,
        CleanProductImageCacheConfig $CleanProductImageCacheConfig
    ) {
        $this->cacheStrategyPool = $cacheStrategyPool;
        $this->CleanProductImageCacheConfig = $CleanProductImageCacheConfig;
    }

    /**
     * On product save clean all cached images to use new images having the same name
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->CleanProductImageCacheConfig->isEnable()) {
            $product = $observer->getProduct();

            /** @var \Blackbird\CleanProductImageCache\Api\CleanCacheStrategyInterface $strategy */
            foreach ($this->cacheStrategyPool->getStrategies() as $strategy) {
                $strategy->clean($product);
            }
        }
    }
}
