<?php
declare(strict_types=1);


namespace Blackbird\CleanProductImageCache\Observer;

use Blackbird\CleanProductImageCache\Model\Config;
use Blackbird\CleanProductImageCache\Model\Config as CleanProductImageCacheConfig;
use Blackbird\CleanProductImageCache\Model\Service\CleanCacheStrategyPool;
use Magento\Framework\App\State;
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
    protected CleanProductImageCacheConfig $cleanProductImageCacheConfig;

    /**
     * @var \Magento\Framework\App\State
     */
    protected State $state;

    /**
     * @param \Blackbird\CleanProductImageCache\Model\Service\CleanCacheStrategyPool $cacheStrategyPool
     * @param \Blackbird\CleanProductImageCache\Model\Config $cleanProductImageCacheConfig
     */
    public function __construct(
        CleanCacheStrategyPool $cacheStrategyPool,
        CleanProductImageCacheConfig $cleanProductImageCacheConfig,
        State $state
    ) {
        $this->cacheStrategyPool = $cacheStrategyPool;
        $this->cleanProductImageCacheConfig = $cleanProductImageCacheConfig;
        $this->state = $state;
    }

    /**
     * On product save clean all cached images to use new images having the same name
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->canExecute()) {
            $product = $observer->getProduct();

            /** @var \Blackbird\CleanProductImageCache\Api\CleanCacheStrategyInterface $strategy */
            foreach ($this->cacheStrategyPool->getStrategies() as $strategy) {
                $strategy->clean($product);
            }
        }
    }

    protected function canExecute()
    {
        $area = $this->state->getAreaCode();
        $allowedAreas = $this->cleanProductImageCacheConfig->getAreas();

        return $this->cleanProductImageCacheConfig->isEnable()
            && \in_array($area, $allowedAreas, true);
    }
}
