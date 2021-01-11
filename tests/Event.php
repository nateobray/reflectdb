<?php

namespace obray\tests;

use \obray\reflectdb\dataTypes\Integer;
use \obray\reflectdb\dataTypes\Varchar50;
use \obray\reflectdb\dataTypes\DateTime;
use \obray\reflectdb\dataTypes\Boolean;

class Event extends \obray\reflectdb\DB
{
    private $table = 'Event';
    private $tablePrimaryKey = 'event_id';

    protected Integer $col_event_id;
    protected Integer $col_account_id;
    protected Integer $col_venue_id;
    protected Integer $col_survey_id;
    protected Varchar50 $col_event_name;
    protected DateTime $col_event_start;
    protected DateTime $col_event_end;
    protected Boolean $col_event_is_active;
}