<?php
declare(strict_types=1);

namespace Blackbird\CleanProductImageCache\Model\Config\Source;

use Magento\Framework\App\Area;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AppArea
 * @package ${NAMESPACE}
 **/
class AppArea implements OptionSourceInterface
{
    protected array $allowedAreas = [
        Area::AREA_ADMINHTML,
        Area::AREA_FRONTEND,
        Area::AREA_WEBAPI_REST,
        Area::AREA_WEBAPI_SOAP,
        Area::AREA_CRONTAB,
        Area::AREA_GRAPHQL
    ];

    /**
     * {@inheritDoc}
     */
    public function toOptionArray(): array
    {
        $result = [];

        foreach ($this->allowedAreas as $area) {
            $result[] = [
                'value' => $area,
                'label' => ucfirst(str_replace('_', ' ', $area))
            ];
        }

        return $result;
    }
}
