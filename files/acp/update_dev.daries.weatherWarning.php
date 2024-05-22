<?php

use wcf\system\WCF;

/**
 * @author  Marco Daries", Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */

$sql = "TRUNCATE wcf1_weather_warning_region";
$statement = WCF::getDB()->prepare($sql);
$statement->execute();
