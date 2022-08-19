<?php

namespace App\Square\Interfaces;

interface FlagContract
{
    /**
     * @return FlagContract
     */
    public function flag(): FlagContract;

    /**
     * @return FlagContract
     */
    public function unFlag(): FlagContract;

    /**
     * @return bool
     */
    public function isFlagged(): bool;
}