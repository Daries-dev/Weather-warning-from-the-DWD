<?php

namespace wcf\system\box;

use wcf\system\WCF;
use wcf\system\weather\warning\UserWeatherWarningHandler;

/**
 * Box that shows the region warning weather information.
 * 
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
class WeatherWarningRegionBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected function loadContent(): void
    {
        if (MODULE_WEATHER_WARNING) {
            if (WCF::getUser()->userID && !WCF::getUser()->getUserOption('weatherWarningRegionEnable')) return;
            if (!WCF::getUser()->userID && empty(UserWeatherWarningHandler::getInstance()->getRegion())) return;

            $this->content = WCF::getTPL()->fetch(
                'boxWeatherWarningRegion',
                'wcf',
                [
                    'region' => UserWeatherWarningHandler::getInstance()->getRegion(),
                    'warnings' => UserWeatherWarningHandler::getInstance()->getWarnings()
                ],
                true
            );
        }
    }
}
