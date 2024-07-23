<?php

namespace wcf\system\weather\warning;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use wcf\system\io\HttpFactory;
use wcf\system\registry\RegistryHandler;
use wcf\system\SingletonFactory;
use wcf\util\JSON;

/**
 * Weather warning handler.
 * 
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */
final class WeatherWarningHandler extends SingletonFactory
{
    /**
     * URL to the forest fire hazard index in Germany.
     */
    const GERMANY_FORESTFIREHAZARDINDEXWBI_URL = 'https://www.dwd.de/DWD/warnungen/agrar/wbx/wbx_stationen.png';

    /**
     * URL to grassland fire index in Germany.
     */
    const GERMANY_GRASSLANDFIREINDEX_URL = 'https://www.dwd.de/DWD/warnungen/agrar/glfi/glfi_stationen.png';

    /**
     * URL for regional weather warnings in Germany.
     */
    const GERMANY_REGION_URL = 'https://www.dwd.de/DWD/warnungen/warnapp/json/warnings.json';

    /**
     * URL for the map of Germany with warnings.
     */
    const GERMANY_MAP_URL = 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de.png';

    /**
     * Package name for the registration action.
     */
    const PACKAGE_NAME = "dev.daries.weatherWarning";

    /**
     * Returns the forest fire hazard index.
     */
    public function getForestFireHazardIndexWBI(): string
    {
        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "forestFireHazardIndexWBI") ?? "";
    }

    /**
     * Returns the map of Germany.
     */
    public function getGermanyMap(): string
    {
        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "germanyMap") ?? "";
    }

    /**
     * Returns the grassland fire index.
     */
    public function getGrasslandFireIndex(): string
    {
        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "grasslandFireIndex") ?? "";
    }

    /**
     * Creates and configures an HTTP client with a timeout setting of 30 seconds.
     */
    private function getHttpClient(): ClientInterface
    {
        return HttpFactory::makeClientWithTimeout(30);
    }

    /**
     * Returns the weather warnings.
     */
    public function getWeatherAlerts(): array
    {
        $weatherAlerts = RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "weatherAlerts");
        return $weatherAlerts !== null ? \unserialize($weatherAlerts) : [];
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $lastUpdate = RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "lastUpdate");
        if ($lastUpdate === null || $lastUpdate < TIME_NOW - 600) {
            RegistryHandler::getInstance()->set(self::PACKAGE_NAME, "lastUpdate", TIME_NOW);

            if (WEATHER_WARNING_ENABLE_FOREST_FIRE_HAZARD_INDEX_WBI) {
                // load germany forest fire index wbi map
                $this->loadImage('forestFireHazardIndexWBI', self::GERMANY_FORESTFIREHAZARDINDEXWBI_URL);
            }

            if (WEATHER_WARNING_ENABLE_GRASSLAND_FIRE_INDEX) {
                // load germany grassland fire index map
                $this->loadImage('grasslandFireIndex', self::GERMANY_GRASSLANDFIREINDEX_URL);
            }

            // load germany map
            $this->loadImage('germanyMap', self::GERMANY_MAP_URL);

            // load region warning information
            $request = new Request('GET', self::GERMANY_REGION_URL, [
                'accept' => 'application/json',
            ]);

            $weatherAlerts = [];
            try {
                $response = $this->getHttpClient()->send($request);
                $parsed = (string)$response->getBody();
                $parsed = \str_replace('warnWetter.loadWarnings(', '', $parsed);
                $parsed = \mb_substr($parsed, 0, -2);

                try {
                    $weatherAlerts = JSON::decode($parsed);
                } catch (SystemException $e) {
                    if (ENABLE_DEBUG_MODE) {
                        throw $e;
                    }
                }
            } catch (TransferException $e) {
                // nothings
            }

            if (!empty($weatherAlerts)) {
                RegistryHandler::getInstance()->set(self::PACKAGE_NAME, 'weatherAlertsTime', ($weatherAlerts['time'] ?? 0) / 1000);
                
                $warnings = \array_merge_recursive(
                    $this->readWeatherAlerts($weatherAlerts['warnings'] ?? []),
                    $this->readWeatherAlerts($weatherAlerts['vorabInformation'] ?? [])
                );
                // TODO: #6 Check to see if the sortWarnings method is still needed
                //$this->sortWarnings($data['weatherAlerts']);

                RegistryHandler::getInstance()->set(self::PACKAGE_NAME, "weatherAlerts", \serialize($warnings));
            }
        }
    }

    /**
     * Loads an image from a specified URL and saves it as a base64-encoded string in the registry.
     */
    private function loadImage(string $name, string $url)
    {
        $dataString = "";
        $response = null;

        $request = new Request('GET', $url, ['accept' => 'image/*',]);
        try {
            $response = $this->getHttpClient()->send($request);

            while (!$response->getBody()->eof()) {
                try {
                    $dataString .= $response->getBody()->read(8192);
                } catch (\RuntimeException $e) {
                    return;
                }
            }
        } catch (TransferException $e) {
            return;
        } finally {
            if ($response && $response->getBody()) {
                $response->getBody()->close();
            }

            RegistryHandler::getInstance()->set(
                self::PACKAGE_NAME,
                $name,
                "data:image/png;base64," . base64_encode($dataString)
            );
        }
    }

    /**
     * Reads weather alerts and sorts by region.
     */
    private function readWeatherAlerts(array $weatherAlerts): array
    {
        $list = [];
        if (empty($weatherAlerts)) return $list;

        foreach ($weatherAlerts as $infos) {
            foreach ($infos as $info) {
                $weatherWarning = WeatherWarning::createWarning($info);
                $list[$weatherWarning->getRegionName()] ??= [];
                $list[$weatherWarning->getRegionName()][] = $weatherWarning;
            }
        }
        return $list;
    }

    /**
     * Sorts the alerts by 'level', 'start' and 'end' per region area.
     */
    private function sortWarnings(array &$warnings): void
    {
        foreach ($warnings as $regionName => $warningDatas) {
            $end = \array_column($warningDatas, 'end');
            $level = \array_column($warningDatas, 'level');
            $start = \array_column($warningDatas, 'start');

            \array_multisort($level, SORT_ASC, $start, SORT_ASC, $end, SORT_ASC, $warningDatas);
        }
    }
}
