<?php

namespace wcf\system\box;

use wcf\system\cache\builder\WeatherWarningCacheBuilder;
use wcf\system\WCF;

/**
 * Box that shows the german warning weather map.
 * 
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/license-for-free-plugins>
 */
class WeatherWarningGermanyBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected function loadContent(): void
    {
        if (MODULE_WEATHER_WARNING) {
            if (WCF::getUser()->userID && !WCF::getUser()->getUserOption('weatherWarningGermanyEnable')) return;

            $data = [
                'germanyMap' => WeatherWarningCacheBuilder::getInstance()->getData([], 'germanyMap'),
                'germanyMapInfo' => WCF::getPath() . 'images/weather/germanyMapInfo.png'
            ];

            $this->content = WCF::getTPL()->fetch('boxWeatherWarningGermany', 'wcf', $data, true);
        }
    }
}
