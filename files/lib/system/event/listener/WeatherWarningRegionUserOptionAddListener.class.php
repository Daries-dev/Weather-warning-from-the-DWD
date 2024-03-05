<?php

namespace wcf\system\event\listener;

use wcf\acp\form\UserOptionAddForm;

/**
 * Inserts 'weatherWarningRegion' in the available option types of user option
 * 
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
class WeatherWarningRegionUserOptionAddListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        UserOptionAddForm::$availableOptionTypes[] = 'weatherWarningRegion';
    }
}
