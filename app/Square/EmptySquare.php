<?php

namespace App\Square;

use App\Square\Interfaces\RevealContract;

class EmptySquare extends AbstractSquare
{
    /**
     * @inheritDoc
     */
    public function reveal(): RevealContract
    {
        $this->reveled = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function canAutoReveal(): bool
    {
        return !$this->isFlagged() && $this->numberOfMines() < 2;
    }


    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        if ($this->isReveled()) {
            return self::SQUARE_REVELED;
        }

        if ($this->isFlagged()) {
            return self::SQUARE_FLAGGED;
        }

        return parent::__toString();
    }
}
