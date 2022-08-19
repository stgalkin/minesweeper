<?php

namespace App\Square;

use App\Square\Interfaces\FlagContract;
use App\Square\Interfaces\RevealContract;
use App\Square\Interfaces\SquareContract;
use Illuminate\Support\Collection;

/**
 * Base square class
 */
abstract class AbstractSquare implements SquareContract
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
     * @var bool
     */
    private $flagged;

    /**
     * @var bool
     */
    protected $reveled;

    /**
     * @var Collection
     */
    private $aroundSquares;

    /**
     * @param int $row
     * @param int $col
     * @param bool $flagged
     * @param bool $reveled
     */
    public function __construct(
        int $row,
        int $col,
        bool $flagged = false,
        bool $reveled = false
    ) {
        $this->row = $row;
        $this->col = $col;
        $this->flagged = $flagged;
        $this->reveled = $reveled;
        $this->aroundSquares = collect();
    }

    /**
     * @inheritDoc
     */
    public function flag(): FlagContract
    {
        $this->flagged = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unFlag(): FlagContract
    {
        $this->flagged = false;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function isFlagged(): bool
    {
        return $this->flagged;
    }

    /**
     * @inheritDoc
     */
    public function isReveled(): bool
    {
        return $this->reveled;
    }

    /**
     * @inheritDoc
     */
    public function row(): int
    {
        return $this->row;
    }

    /**
     * @inheritDoc
     */
    public function col(): int
    {
        return $this->col;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::SQUARE_NOT_REVELED;
    }

    /**
     * @inheritDoc
     */
    public function aroundSquares(): Collection
    {
        return $this->aroundSquares;
    }

    /**
     * @inheritDoc
     */
    public function aroundNonMinesSquares(): Collection
    {
        return $this->aroundSquares->filter(function(SquareContract $square) {
            return !$square instanceof MineSquare;
        });
    }

    /**
     * @inheritDoc
     */
    public function addNeighbours(Collection $squares): SquareContract
    {
        $this->aroundSquares = $squares;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function numberOfMines(): int
    {
        return $this->aroundSquares()->filter(function(SquareContract $square) {
            return $square instanceof MineSquare;
        })->count();
    }
}
