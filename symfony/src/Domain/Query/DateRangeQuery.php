<?php

namespace App\Domain\Query;

use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\DateInterval;


class DateRangeQuery
{
    public function __construct(private \DateTime $date)
    {
    }
    
    public function getDate() : \DateTime
    {
        return $this->date;
    }
}