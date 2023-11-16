<?php

namespace App\Domain\Query;


use DateTime;
use DateTimeZone;
use DateInterval;

use Doctrine\Common\Util\Debug;

class DateRangeHandler
{

    private $startDate;
    private $endDate;

    public function __construct(private DateRangeQuery $query)
    {
        /* 
         * $query: DateRangeQuery / It's the query to handle
         */

    }
    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }
    public function handle()
    {
        $date = ($this->query->getDate())->format('Y-m-d');
        $date .= " 00:00:00";
        $this->startDate = new \DateTime($date);
        $this->endDate = new \DateTime($date);
        $this->endDate->modify('+1 day');
    }
}