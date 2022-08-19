<?php

namespace App\Square;

use App\Exceptions\GameOverException;
use App\Square\Interfaces\RevealContract;

class MineSquare extends AbstractSquare
{
    /**
     * @param int $row
     * @param int $col
     * @param bool $flagged
     */
    public function __construct(
        int $row,
        int $col,
        bool $flagged = false
    ) {
        parent::__construct($row, $col, $flagged);
    }

    /**
     * @inheritDoc
     */
    public function reveal(): RevealContract
    {
        throw new GameOverException("Game over. You revel a mine");
    }

    /**
     * @inheritDoc
     */
    public function canAutoReveal(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        if ($this->isFlagged()) {
            return self::SQUARE_MINE_FLAGGED;
        }

        return self::SQUARE_MINE;
    }
}
