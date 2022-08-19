<?php

namespace App\Models;

use App\Exceptions\GameIsNotNewException;
use App\Exceptions\GameNotStartedException;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public const STATUS_NEW = 0;
    public const STATUS_STARTED = 1;
    public const STATUS_PLAYER_WON = 2;
    public const STATUS_PLAYER_LOOSE = 3;

    public const GAME_ENDED_STATUSES = [
        self::STATUS_PLAYER_WON,
        self::STATUS_PLAYER_LOOSE,
    ];

    /**
     * @var Grid|null
     */
    private $grid;

    protected $fillable = [
        'rows',
        'cols',
        'status',
        'squares',
    ];

    /**
     * @var int[]
     */
    protected $attributes = [
        'status' => self::STATUS_NEW,
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $casts = [
        'squares' => 'array',
    ];

    /**
     * @return $this
     * @throws GameIsNotNewException
     */
    public function start(): self
    {
        if (!$this->isNew()) {
            throw new GameIsNotNewException("Game is not new");
        }

        $this->status = self::STATUS_STARTED;

        $this->save();

        return $this;
    }

    /**
     * @return $this
     * @throws GameNotStartedException
     */
    public function playerWon(): self
    {
        if (!$this->isStarted()) {
            throw new GameNotStartedException("Game is not started");
        }

        $this->status = self::STATUS_PLAYER_WON;
        $this->save();

        return $this;
    }

    /**
     * @return $this
     * @throws GameNotStartedException
     */
    public function playerLoose(): self
    {
        if (!$this->isStarted()) {
            throw new GameNotStartedException("Game is not started");
        }

        $this->status = self::STATUS_PLAYER_LOOSE;
        $this->save();

        return $this;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->status === self::STATUS_STARTED;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return in_array($this->status, [self::STATUS_PLAYER_WON, self::STATUS_PLAYER_LOOSE]);
    }

    /**
     * @return Grid
     */
    public function getGrid(): Grid
    {
        if (!$this->grid instanceof Grid) {
            $this->grid = new Grid($this, $this->squares ?? []);
        }

        return $this->grid;
    }

    /**
     * Save Game entity with actual grid squares
     * @return void
     */
    public function saveWithGrid()
    {
        $this->squares = $this->getGrid()->toArray();
        $this->save();
    }
}