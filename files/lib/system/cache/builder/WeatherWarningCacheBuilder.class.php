<?php

namespace wcf\system\cache\builder;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use wcf\system\exception\SystemException;
use wcf\system\io\HttpFactory;
use wcf\util\JSON;

/**
 * Caches weather warning data from DWD
 * 
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */
class WeatherWarningCacheBuilder extends AbstractCacheBuilder
{
    const GERMANY_FORESTFIREHAZARDINDEXWBI_URL = 'https://www.dwd.de/DWD/warnungen/agrar/wbx/wbx_stationen.png';
    const GERMANY_GRASSLANDFIREINDEX_URL = 'https://www.dwd.de/DWD/warnungen/agrar/glfi/glfi_stationen.png';
    const GERMANY_REGION_URL = 'https://www.dwd.de/DWD/warnungen/warnapp/json/warnings.json';
    const GERMANY_MAP_URL = 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de.png';

    /**
     * @inheritDoc
     */
    protected $maxLifetime = 600;

    private function getHttpClient(): ClientInterface
    {
        return HttpFactory::makeClientWithTimeout(5);
    }

    private function loadImage(string $name, string $url, array &$data)
    {
        $request = new Request('GET', $url, ['accept' => 'image/*',]);

        $dataString = "";
        try {
            $response = $this->getHttpClient()->send($request);

            while (!$response->getBody()->eof()) {
                try {
                    $dataString .= $response->getBody()->read(8192);
                } catch (\RuntimeException $e) {
                    throw new \DomainException(
                        'Failed to read response body.',
                        0,
                        $e
                    );
                }
            }
        } catch (TransferException $e) {
            throw new \DomainException('Failed to request', 0, $e);
        } finally {
            if ($response && $response->getBody()) {
                $response->getBody()->close();
            }

            $data[$name] = "data:image/png;base64," . base64_encode($dataString);
        }
    }

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'forestFireHazardIndexWBI' => '',
            'germanyMap' => '',
            'grasslandFireIndex' => '',
            'warnings' => []
        ];

        if (WEATHER_WARNING_ENABLE_FOREST_FIRE_HAZARD_INDEX_WBI) {
            // load germany forest fire index wbi map
            $this->loadImage('forestFireHazardIndexWBI', self::GERMANY_FORESTFIREHAZARDINDEXWBI_URL, $data);
        }

        if (WEATHER_WARNING_ENABLE_GRASSLAND_FIRE_INDEX) {
            // load germany grassland fire index map
            $this->loadImage('grasslandFireIndex', self::GERMANY_GRASSLANDFIREINDEX_URL, $data);
        }

        // load germany map
        $this->loadImage('germanyMap', self::GERMANY_MAP_URL, $data);

        // load region warning information
        $request = new Request('GET', self::GERMANY_REGION_URL, [
            'accept' => 'application/json',
        ]);
        $response = $this->getHttpClient()->send($request);
        $parsed = (string)$response->getBody();
        $parsed = \str_replace('warnWetter.loadWarnings(', '', $parsed);
        $parsed = \mb_substr($parsed, 0, -2);
        $data['warnings'] = $this->readWarnings(JSON::decode($parsed)['warnings']);
        $this->sortWarnings($data['warnings']);

        return $data;
    }

    /**
     * Reads the data from DWD and sorts them by region.
     * Returns a list by region.
     */
    protected function readWarnings(array $warnings): array
    {
        $list = [];
        if (empty($warnings)) return $list;

        foreach ($warnings as $warningDatas) {
            foreach ($warningDatas as $warning) {
                if (!isset($list[$warning['regionName']])) $list[$warning['regionName']] = [];

                $newEntry = [
                    'altitudeStart' => $warning['altitudeStart'],
                    'altitudeEnd' => $warning['altitudeEnd'],
                    'description' => $warning['description'],
                    'end' => ($warning['end'] / 1000),
                    'event' => $this->umlautsConvert($warning['event']),
                    'headline' => $warning['headline'],
                    'instruction' => $warning['instruction'],
                    'level' => $warning['level'],
                    'regionName' => $warning['regionName'],
                    'start' => ($warning['start'] / 1000),
                    'state' => $warning['state'],
                    'stateShort' => $warning['stateShort'],
                    'type' => $warning['type']
                ];

                $exist = false;
                foreach ($list[$warning['regionName']] as $entry) {
                    if (empty(\array_diff($entry, $newEntry))) {
                        $exist = true;
                    }
                }

                if (!$exist) {
                    $list[$warning['regionName']][] = $newEntry;
                }
            }
        }

        return $list;
    }

    /**
     * Sorts the warnings by 'level', 'start' and 'end' per region area.
     */
    protected function sortWarnings(array &$warnings): void
    {
        foreach ($warnings as $regionName => $warningDatas) {
            $end = \array_column($warningDatas, 'end');
            $level = \array_column($warningDatas, 'level');
            $start = \array_column($warningDatas, 'start');

            \array_multisort($level, SORT_ASC, $start, SORT_ASC, $end, SORT_ASC, $warningDatas);
        }
    }

    /**
     * Converts umlauts and returns the changed value
     */
    protected function umlautsConvert(string $value): string
    {
        $value = \strtolower($value);

        $search = [" ", "ä", "Ä", "ö", "Ö", "ü", "Ü", "ß"];
        $replace = ["_", "ae", "ae", "oe", "oe", "ue", "ue", "ss"];
        $patched = \str_replace($search, $replace, $value);

        return $patched;
    }
}
