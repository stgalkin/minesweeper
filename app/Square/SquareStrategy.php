<?php

namespace App\Square;

use App\Exceptions\UndefinedSquareType;
use App\Square\Interfaces\SquareContract;

class SquareStrategy
{
    /**
     * @param $value
     * @param $row
     * @param $col
     * @return SquareContract
     */
    public static function createSquare($value, $row, $col): SquareContract
    {
        switch ($value) {
            case SquareContract::SQUARE_FLAGGED:
                return new EmptySquare($row, $col, true);
            case null:
            case SquareContract::SQUARE_NOT_REVELED:
                return new EmptySquare($row, $col);
            case SquareContract::SQUARE_REVELED:
                return new EmptySquare($row, $col, false, true);
            case SquareContract::SQUARE_MINE_FLAGGED:
                return new MineSquare($row, $col, true);
            case SquareContract::SQUARE_MINE:
                return new MineSquare($row, $col);
            default:
                throw new UndefinedSquareType("Cant resolve square type {$value}");
        }
    }
}