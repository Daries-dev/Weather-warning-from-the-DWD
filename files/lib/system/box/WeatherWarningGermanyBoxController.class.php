<?php

namespace wcf\system\box;

use wcf\system\WCF;
use wcf\system\weather\warning\WeatherWarningHandler;

/**
 * Box that shows the german warning weather map.
 * 
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */
class WeatherWarningGermanyBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected function loadContent(): void
    {
        if (!MODULE_WEATHER_WARNING) {
            return;
        }

        $user = WCF::getUser();
        if (!$user->userID || !$user->getUserOption('weatherWarningGermanyEnable')) {
            return;
        }

        $germanyMap = WeatherWarningHandler::getInstance()->getGermanyMap();

        $this->content = WCF::getTPL()->fetch(
            'boxWeatherWarningGermany',
            'wcf',
            [
                'germanyMap' => $germanyMap,
            ],
            true
        );
    }
}
