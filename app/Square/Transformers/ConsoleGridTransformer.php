<?php

namespace App\Square\Transformers;

use App\Models\Grid;
use App\Square\Interfaces\SquareContract;
use Illuminate\Support\Collection;

class ConsoleGridTransformer implements GridTransformerContract
{
    /**
     * @inheritDoc
     */
    public static function transform(Grid $grid, bool $devMode = false): string
    {
        $groupedGrid = $grid->getSquares()->groupBy(function(SquareContract $square) {
            return $square->row();
        });

        $response = ' |'.implode('|', range(1, count($groupedGrid->first())));

        $groupedGrid->each(function(Collection $grid, int $row) use (&$response) {
            $response .= PHP_EOL.$row.'|';

            $grid->each(function(SquareContract $square) use (&$response) {
                $response .= self::convert($square).'|';
            });
        });

        return $response;
    }

    /**
     * @param \App\Square\Interfaces\SquareContract $square
     * @return string
     */
    private static function convert(SquareContract $square): string
    {
        switch (true) {
            case $square->isReveled():
                $numberOfMines = $square->numberOfMines();
                return $numberOfMines > 0 ? $numberOfMines : SquareContract::SQUARE_REVELED;
            case $square->isFlagged():
                return SquareContract::SQUARE_FLAGGED;
            default:
                return SquareContract::SQUARE_NOT_REVELED;
        }
    }
}