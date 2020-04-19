<?php
namespace App\Data;

use App\Entity\Dog;

class SearchData
{

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Dog[]
     */
    public $dog = [];

}