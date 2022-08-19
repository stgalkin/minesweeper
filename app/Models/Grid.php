<?php

namespace App\Models;

use App\Exceptions\GameIsNotNewException;
use App\Exceptions\GameNotStartedException;
use App\Exceptions\GameOverException;
use App\Exceptions\SquareNotFoundException;
use App\Square\Interfaces\SquareContract;
use App\Square\SquareStrategy;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Grid
{
    /**
     * @var Game
     */
    private $game;

    /**
     * @var Collection
     */
    private $squares;

    public function __construct(Game $game, array $squares)
    {
        $this->game = $game;
        $this->initGrid($game->rows, $game->cols);
        $this->fillSquares($squares);
    }

    /**
     * @param int $rows
     * @param int $cols
     * @return array
     */
    public function initGrid(int $rows, int $cols): array
    {
        // create the empty grid
        $squareArray = array_fill(1, $rows * $cols, null);

        $this->squares = collect($squareArray)->map(function ($value, int $index) use ($rows, $cols) {
            $row = ceil($index / $cols);
            $col = ($index % $cols) + 1;

            return SquareStrategy::createSquare($value, $row, $col);
        });

        $this->addNeighbours();

        return $squareArray;
    }

    /**
     * @param SquareContract $square
     * @return bool
     * @throws GameOverException|GameIsNotNewException
     */
    public function revel(SquareContract $square): bool
    {
        $square->reveal();
        $this->revelAround($square);

        if ($this->game->isNew()) {
            $notReveledSquare = $this->getSquares()->filter(function (SquareContract $square) {
                return !$square->isReveled();
            });

            $allSquares = $this->toArray();

            // get the random squares for mines
            $notReveledSquare->random($this->game->mines)->each(function (SquareContract $square) use (&$allSquares) {
                $allSquares[$square->row()][$square->col()] = SquareContract::SQUARE_MINE;
            });

            $this->fillSquares($allSquares);
            $this->game->start();
            // we need it, because we fill the field

            $square = $this->getSquares()->first(function (SquareContract $existing) use ($square) {
                return $existing->col() === $square->col() &&
                    $existing->row() === $square->row();
            });
        }

        $this->revealAllEmpty($square->aroundNonMinesSquares());

        return $this->iwWon();
    }

    /**
     * @param Collection $neighbours
     * @return void
     * @throws GameOverException
     */
    public function revealAllEmpty(Collection $neighbours): void
    {
        if (!$neighbours->isEmpty()) {
            $neighbours->each(function (SquareContract $square) {
                // reveal current square
                $square->reveal();

                // if current square is not close to the mine, get around not reveled squares and revel it.
                if ($square->numberOfMines() === 0) {
                    $this->revealAllEmpty($square->aroundSquares()->filter(function (SquareContract $square) {
                        return $square->canAutoReveal() && !$square->isReveled();
                    }));
                }
            });
        }
    }

    /**
     * @param SquareContract $square
     * @return Grid
     * @throws GameOverException
     */
    private function revelAround(SquareContract $square): Grid
    {
        $square->aroundSquares()->each(function (SquareContract $neighbour) {
            if ($neighbour->canAutoReveal()) {
                $neighbour->reveal();
            }
        });

        return $this;
    }

    /**
     * @return void
     * @throws GameIsNotNewException
     */
    public function addMines()
    {
        if (!$this->game->isNew()) {
            throw new GameIsNotNewException("Game is not new");
        }
    }

    /**
     * @param array $squares
     * @return Grid
     */
    private function fillSquares(array $squares): Grid
    {
        if (count($squares) === 0) {
            return $this;
        }

        $this->squares = $this->getSquares()->map(function (SquareContract $square) use (
            $squares
        ) {
            $value = Arr::get($squares, "{$square->row()}.{$square->col()}");

            return SquareStrategy::createSquare($value, $square->row(), $square->col());
        });

        $this->addNeighbours();

        return $this;
    }

    /**
     * @return void
     */
    private function addNeighbours(): void
    {
        $this->getSquares()->each(function (SquareContract $square) {
            $square->addNeighbours($this->getSquares()->filter(function (SquareContract $neighbour) use ($square
            ) {
                //skip this square
                if ($neighbour->col() === $square->col() && $neighbour->row() === $square->row()) {
                    return false;
                }

                // find my neighbours. If this square cords are 2:3. I need to find another squares in:
                // 1:2, 1:3, 1:4, 2:2, 2:4, 3:2, 3:3, 3,4,
                return ($neighbour->row() >= ($square->row() - 1) && $neighbour->row() <= ($square->row() + 1)) &&
                    ($neighbour->col() >= ($square->col() - 1) && $neighbour->col() <= ($square->col() + 1));
            }));
        });
    }

    /**
     * @return bool
     * @throws GameNotStartedException
     */
    public function iwWon(): bool
    {
        $totalMines = $this->game->mines;

        $isWon = $this->getSquares()->count() === ($this->getSquares()->filter(function (SquareContract $square) {
                return $square->isReveled();
            })->count()) + $totalMines;

        if ($isWon) {
            $this->game->playerWon();
        }

        return $isWon;
    }

    /**
     * @return Collection
     */
    public function getSquares(): Collection
    {
        return $this->squares->sortBy(function (SquareContract $square) {
            return $square->row().$square->col();
        });
    }

    /**
     * @param int $row
     * @param int $col
     * @return SquareContract
     * @throws SquareNotFoundException
     */
    public function getSquareByRowAndCol(int $row, int $col): SquareContract
    {
        $square = $this->getSquares()->first(function(SquareContract $square) use ($row, $col) {
            return $square->row() === $row && $square->col() === $col;
        });

        if (!$square instanceof SquareContract) {
            throw new SquareNotFoundException('Square was not found');
        }

        return $square;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->getSquares()->groupBy(function (SquareContract $square) {
            return $square->row();
        })
            ->map(function (Collection $squares) {
                return $squares->mapWithKeys(function (SquareContract $square) {
                    return [$square->col() => (string)$square];
                });
            })->toArray();
    }
}