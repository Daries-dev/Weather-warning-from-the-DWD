<?php

namespace wcf\system\weather\warning;

use wcf\system\cache\builder\WeatherWarningCacheBuilder;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */
class UserWeatherWarningHandler extends SingletonFactory
{
    /**
     * region
     */
    protected string $region = '';

    /**
     * All weather warnings from DWD.
     */
    protected array $warnings = [];

    /**
     * Returns the current region.
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * Returns the weather warnings for the current region.
     */
    public function getWarnings(): array
    {
        return $this->warnings[$this->getRegion()] ?? [];
    }

    /**
     * Returns false if has no warnings.
     */
    public function hasWarnings(): bool
    {
        return !empty($this->getWarnings());
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        if (MODULE_WEATHER_WARNING) {
            $this->warnings = WeatherWarningCacheBuilder::getInstance()->getData([], 'weatherAlerts');

            $this->setRegion(WEATHER_WARNING_DEFAULT_REGION);

            if (WCF::getUser()->userID) {
                $region = WCF::getUser()->getUserOption('weatherWarningRegion');

                if ($region !== null && !empty($region)) {
                    $this->setRegion($region);
                }
            }

            EventHandler::getInstance()->fireAction($this, 'init');
        }
    }

    /**
     * Sets the region
     */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }
}
