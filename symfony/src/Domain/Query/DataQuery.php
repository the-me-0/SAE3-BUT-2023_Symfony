<?php

namespace App\Domain\Query;

class DataQuery
{

    private int $tag;
    private string $name;
    private int $limit;
    private string $date1;
    private string $date2;

    public function __construct(int $acquisitionDeviceTag, string $name='', int $limit=0, string $date1='', string $date2='')
    {
        $this->tag = $acquisitionDeviceTag;
        $this->name = $name;
        $this->limit = $limit;

        /* Safety checks */
        /* Verifies that the dates are in the correct format AAAA-MM-JJ HH:MM:SS */
        if($date1 != '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date1)) {
            throw new \Exception('La date de début n\'est pas au bon format. Utiliser le format AAAA-MM-JJ');
        }
        if($date2 != '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date2)) {
            throw new \Exception('La date de fin n\'est pas au bon format. Utiliser le format AAAA-MM-JJ');
        }
        
        $this->date1 = $date1;
        $this->date2 = $date2;
    }

    /* This setter allows to use the same query instead of creating a new one for the same sensor */
    public function setName(string $name):void
    {
        $this->name = $name;
    }

    /* This setter allows to use the same query instead of creating a new one for the same sensor */
    public function setTag(int $tag):void
    {
        $this->tag = $tag;
    }

    public function setDate1(string $date1):void
    {
        $this->date1 = $date1;
    }

    public function setDate2(string $date2):void
    {
        $this->date2 = $date2;
    }

    
    /* ~~~~~~~~~~~~~~~~~~ GETTERS ~~~~~~~~~~~~~~~~~~ */

    public function getLimit():int
    {
        return $this->limit;
    }

    public function getTag():int
    {
        return $this->tag;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getDate1():string
    {
        return $this->date1;
    }

    public function getDate2():string
    {
        return $this->date2;
    }
}
