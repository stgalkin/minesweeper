<?php

namespace App\Square\Transformers;

use App\Models\Grid;

interface GridTransformerContract
{
    /**
     * @param Grid $grid
     * @param bool $devMode
     * @return string
     */
    public static function transform(Grid $grid, bool $devMode = false): string;
}