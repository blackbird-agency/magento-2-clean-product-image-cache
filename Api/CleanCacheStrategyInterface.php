<?php
declare(strict_types=1);

namespace Blackbird\CleanProductImageCache\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface CleanCacheStrategyInterface
{
    /**
     * Delete cached product image
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return void
     */
    public function clean(ProductInterface $product): void;
}
