<?php
declare(strict_types=1);


namespace Blackbird\CleanProductImageCache\Model\Service;

use Blackbird\CleanProductImageCache\Api\CleanCacheStrategyInterface;

/**
 * Class CleanAllImageCache
 * @package Blackbird\CleanProductImageCache\Model\Service
 **/
class CleanCacheStrategyPool
{
    /**
     * @var array
     */
    protected array $cleanCacheStrategies;

    /**
     * @param array $cleanCacheStrategies
     */
    public function __construct(
        array $cleanCacheStrategies
    ) {
        $this->cleanCacheStrategies = array_filter(
            $cleanCacheStrategies,
            static function ($item) {
                return (!isset($item['disable']) || !$item['disable']) && $item['class'];
            }
        );
        uasort($this->cleanCacheStrategies, [$this, 'compareSortOrder']);
    }

    /**
     * Execute each image cache cleaning strategies
     *
     * @return array
     */
    public function getStrategies(): array
    {
        $strategies = [];

        foreach ($this->cleanCacheStrategies as $strategy) {
            $strategies[] = $strategy['class'];
        }

        return $strategies;
    }

    protected function compareSortOrder($dataFirst, $dataSecond): int
    {
        return (int)$dataFirst['sortOrder'] <=> (int)$dataSecond['sortOrder'];
    }
}
