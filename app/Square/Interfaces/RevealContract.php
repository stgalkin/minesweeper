<?php

namespace App\Square\Interfaces;

use App\Exceptions\GameOverException;

interface RevealContract
{
    /**
     * @return bool
     */
    public function isReveled(): bool;

    /**
     * @return RevealContract
     * @throws GameOverException
     */
    public function reveal(): RevealContract;

    /**
     * @return bool
     */
    public function canAutoReveal(): bool;
}