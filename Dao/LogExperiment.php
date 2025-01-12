<?php

namespace Piwik\Plugins\SimpleABTesting\Dao;

use Piwik\Common;
use Piwik\Date;
use Piwik\Db;
use Piwik\DbHelper;

class LogExperiment
{
    const TABLE_NAME = 'log_crash';

    const DEFAULT_DAYS_UNTIL_CONSIDERED_DISAPPEARED = 7;

    const MAX_LENGTH_MESSAGE = 255;
    const MAX_LENGTH_RESOURCE_URI = 300;
    const MAX_LENGTH_CRASH_TYPE = 100;
    const DEFAULT_UPDATE_EVERY_N_HOURS = 3;


    public function record($parameters)
    {


    }







}
