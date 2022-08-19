<?php

namespace App\Square\Interfaces;

use Illuminate\Support\Collection;

interface SquareContract extends RevealContract, FlagContract
{
    public const SQUARE_REVELED = '_';
    public const SQUARE_NOT_REVELED = '?';
    public const SQUARE_FLAGGED = 'f';
    public const SQUARE_MINE_FLAGGED = 'mf';
    public const SQUARE_MINE = 'm';

    /**
     * @return int
     */
    public function row(): int;

    /**
     * @return int
     */
    public function col(): int;

    /**
     * @return Collection
     */
    public function aroundSquares(): Collection;

    /**
     * @return Collection
     */
    public function aroundNonMinesSquares(): Collection;

    /**
     * @param Collection $squares
     * @return SquareContract
     */
    public function addNeighbours(Collection $squares): SquareContract;

    /**
     * @return int
     */
    public function numberOfMines(): int;
}