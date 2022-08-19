<?php

namespace App\Services;

use App\Exceptions\GameOverException;
use App\Exceptions\PropertyIsNotInit;
use App\Exceptions\SquareNotFoundException;
use App\Models\Game;
use App\Square\Interfaces\SquareContract;
use Illuminate\Http\Request;

class GameService
{
    /**
     * @var Game|null
     */
    private $game;

    /**
     * @param Game $game
     * @return $this
     */
    public function workWith(Game $game): GameService
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @param int $row
     * @param int $col
     * @return $this
     * @throws PropertyIsNotInit
     * @throws SquareNotFoundException
     * @throws GameOverException
     */
    public function reveal(int $row, int $col): GameService
    {
        $square = $this->getSquare($row, $col);

        if (!$square->isReveled()) {
            $square->reveal();

            if ($this->getGame()->isNew()) {

            }

            $this->getGame()->saveWithGrid();
        }

        return $this;
    }

    /**
     * @param int $row
     * @param int $col
     * @return $this
     * @throws PropertyIsNotInit
     * @throws SquareNotFoundException
     */
    public function flag(int $row, int $col): GameService
    {
        $square = $this->getSquare($row, $col);

        if ($square->isFlagged()) {
            $square->unFlag();
        } else {
            $square->flag();
        }

        $this->getGame()->saveWithGrid();

        return $this;
    }

    /**
     * @param int $row
     * @param int $col
     * @return SquareContract
     * @throws PropertyIsNotInit
     * @throws SquareNotFoundException
     */
    private function getSquare(int $row, int $col): SquareContract
    {
        $square = $this->getGame()->getSquares()->get("$row.$col");

        if (!$square instanceof SquareContract) {
            throw new SquareNotFoundException('Square not found');
        }

        return $square;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function startGame(Request $request): GameService
    {
        $game = new Game();
        $game->row = $request->get('rows');
        $game->col = $request->get('cols');
        $game->mines = $request->get('mines');
        $game->squares = $game->getGrid()->toArray();

        $game->saveWithGrid();

        return $this->workWith($game);
    }

    /**
     * @return Game
     * @throws PropertyIsNotInit
     */
    private function getGame(): Game
    {
        if (!$this->game instanceof Game) {
            throw new PropertyIsNotInit("Game is not set for this service");
        }

        return $this->game;
    }
}