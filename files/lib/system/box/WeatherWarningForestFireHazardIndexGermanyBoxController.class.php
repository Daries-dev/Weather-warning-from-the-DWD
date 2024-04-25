<?php

namespace wcf\system\box;

use wcf\system\cache\builder\WeatherWarningCacheBuilder;
use wcf\system\WCF;

/**
 * Box that shows the german forest fire hazard index map.
 * 
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */
class WeatherWarningForestFireHazardIndexGermanyBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected function loadContent(): void
    {
        if (MODULE_WEATHER_WARNING && WEATHER_WARNING_ENABLE_FORESTFIREHAZARDINDEXWBI) {
            if (WCF::getUser()->userID && !WCF::getUser()->getUserOption('weatherWarningForestFireHazardIndexGermanyEnable')) return;

            $data = [
                'forestFireHazardIndexMap' => WeatherWarningCacheBuilder::getInstance()->getData([], 'forestFireHazardIndexWBI')
            ];

            $this->content = WCF::getTPL()->fetch('boxWeatherWarningForestFireHazardIndexGermany', 'wcf', $data, true);
        }
    }
}
