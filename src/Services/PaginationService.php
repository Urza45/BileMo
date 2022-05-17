<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    protected $page;
    protected $limit;

    const LIMIT_DEFAULT = 10;

    public function __construct($page = 0, $limit = 5)
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public function verifInteger($chain): bool
    {
        if ($chain == null) {
            return false;
        }
        if (!is_integer((int) $chain)) {
            return false;
        }
        if ((int) $chain < 0) {
            return false;
        }
        return true;
    }
}
