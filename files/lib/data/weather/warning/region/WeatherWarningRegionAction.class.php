<?php

namespace wcf\data\weather\warning\region;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISearchAction;
use wcf\system\exception\UserInputException;

/**
 * Executes region related actions.
 * 
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 * 
 * @method      WeatherWarningRegion            create()
 * @method      WeatherWarningRegionEditor[]    getObjects()
 * @method      WeatherWarningRegionEditor      getSingleObject()
 */
class WeatherWarningRegionAction extends AbstractDatabaseObjectAction implements ISearchAction
{
    /**
     * @inheritDoc
     */
    protected $className = WeatherWarningRegionEditor::class;

    /**
     * @inheritDoc
     */
    public function getSearchResultList(): array
    {
        $searchString = $this->parameters['data']['searchString'];
        $excludedSearchValues = [];
        if (isset($this->parameters['data']['excludedSearchValues'])) {
            $excludedSearchValues = $this->parameters['data']['excludedSearchValues'];
        }
        $list = [];

        $regionList = new WeatherWarningRegionList();
        $regionList->getConditionBuilder()->add("regionName LIKE ?", ['%' . $searchString . '%']);
        if (!empty($excludedSearchValues)) {
            $regionList->getConditionBuilder()->add("regionName NOT IN (?)", [$excludedSearchValues]);
        }
        $regionList->sqlLimit = 10;
        $regionList->readObjects();

        foreach ($regionList as $region) {
            $list[] = [
                'label' => $region->regionName,
                'objectID' => $region->regionID
            ];
        }

        return $list;
    }

    /**
     * @inheritDoc
     */
    public function validateGetSearchResultList(): void
    {
        $this->readString('searchString', false, 'data');

        if (isset($this->parameters['data']['excludedSearchValues']) && !\is_array($this->parameters['data']['excludedSearchValues'])) {
            throw new UserInputException('excludedSearchValues');
        }
    }
}
