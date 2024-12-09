<?php
declare(strict_types=1);

namespace Blackbird\CleanProductImageCache\Model\Service;

use Blackbird\CleanProductImageCache\Api\CleanCacheStrategyInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as MediaGalleryReadHandler;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\Glob;
use Psr\Log\LoggerInterface;

/**
 * Class CleanProductImageCache
 * @package Blackbird\CleanProductImageCache\Model\Service
 **/
class CleanMagentoCacheStrategy implements CleanCacheStrategyInterface
{
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected DirectoryList $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected File $file;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected EventManagerInterface $eventManager;

    protected MediaGalleryReadHandler $mediaGalleryReadHandler;

    /**
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        DirectoryList $directoryList,
        File $file,
        LoggerInterface $logger,
        EventManagerInterface $eventManager,
        MediaGalleryReadHandler $mediaGalleryReadHandler
    ) {
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->logger = $logger;
        $this->eventManager = $eventManager;
        $this->mediaGalleryReadHandler = $mediaGalleryReadHandler;
    }

    /**
     * {@inheritDoc}
     */
    public function clean(ProductInterface $product): void
    {
        $product = $this->mediaGalleryReadHandler->execute($product);
        $productMedia = $product->getMediaGalleryImages()->getColumnValues('file');
        $mediaPath = $this->directoryList->getPath(DirectoryList::MEDIA);
        $productImagePath = $mediaPath . '/catalog/product/cache/';
        $paths = [];

        foreach ($productMedia as $media) {
            $pattern = $productImagePath . '*' . $media;

            foreach (Glob::glob($pattern) as $filename) {
                try {
                    $paths[] = $filename;
                    $this->file->deleteFile($filename);
                } catch (FileSystemException $e) {
                    $this->logger->warning(\sprintf('Cannot delete image cache for product %s : %s',
                        $product->getId(),
                        $e->getMessage()
                    ));
                }
            }
        }

        $this->eventManager->dispatch('blackbird_image_cache_clean_after', ['paths' => $paths]);
    }
}

