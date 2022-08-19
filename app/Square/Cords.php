<?php

namespace App\Square;

/**
 *
 */
class Cords
{
    /**
     * @var int
     */
    private $row;
    /**
     * @var int
     */
    private $col;

    /**
     * @param int $row
     * @param int $col
     */
    public function __construct(int $row, int $col)
    {
        $this->row = $row;
        $this->col = $col;
    }

    /**
     * @return int
     */
    public function getRow(): int
    {
        return $this->row;
    }

    /**
     * @return int
     */
    public function getCord(): int
    {
        return $this->cord;
    }
}
