<?php

namespace App\Console\Commands;

use App\Exceptions\GameOverException;
use App\Models\Game;
use App\Square\Interfaces\SquareContract;
use App\Square\Transformers\ConsoleGridTransformer;
use Illuminate\Console\Command;

class StartGame extends Command
{
    /**
     * Number of reserved squares. Include selected square and all neighbours
     */
    const NUMBER_OF_RESERVED_SQUARES = 9;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start minesweeper game';

    /**
     * @var Game
     */
    private $game;

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \App\Exceptions\GameIsNotNewException
     * @throws \App\Exceptions\GameOverException
     * @throws \App\Exceptions\SquareNotFoundException
     */
    public function handle()
    {
        $this->game = new Game();
        $rows = (int) $this->ask('Number of rows From 5 to 15:');

        if ($rows < 5 || $rows > 15) {
            throw new \InvalidArgumentException('Number of rows should be from 5 to 15');
        }

        $this->game->rows = $rows;

        $cols = (int) $this->ask('Number of cols From 5 to 15:');
        if ($cols < 5 || $cols > 15) {
            throw new \InvalidArgumentException('Number of cols should be from 5 to 15');
        }

        $this->game->cols = $cols;

        $maxMinesNumber = ($rows * $cols) - self::NUMBER_OF_RESERVED_SQUARES;

        $mines = (int) $this->ask("Number of mines From 1 to $maxMinesNumber:");

        if ($mines < 1 || $mines > $maxMinesNumber) {
            throw new \InvalidArgumentException("Number of mines should be from 1 to $maxMinesNumber");
        }

        $this->game->mines = $mines;
        $this->game->saveWithGrid();

        $this->output->info('Game Started');

        $this->output->writeln(ConsoleGridTransformer::transform($this->game->getGrid()));

        $this->reveal();

        while (!in_array($this->game->status, Game::GAME_ENDED_STATUSES)) {
            try {
                $this->act();
            } catch (GameOverException $exception) {
                $this->output->error("Game over!");
                break;
            }
        }

    }

    /**
     * @return void
     * @throws \App\Exceptions\GameIsNotNewException
     * @throws \App\Exceptions\GameOverException
     * @throws \App\Exceptions\SquareNotFoundException
     */
    private function act(): void
    {
        $choice = $this->choice('You want to revel of flag', ['Reveal', 'Flag']);

        if ($choice === 'Reveal') {
            $this->reveal();
        } else {
            $this->flag();
        }
    }

    /**
     * @param \App\Models\Game $game
     * @return void
     * @throws \App\Exceptions\GameIsNotNewException
     * @throws \App\Exceptions\GameOverException
     * @throws \App\Exceptions\SquareNotFoundException
     */
    private function reveal(): void
    {
        $square = $this->getSquareByCords($this->ask('Choose the row and column to reveal. In format ROW,COL'));

        $this->game->getGrid()->revel($square);

        $this->output->writeln(ConsoleGridTransformer::transform($this->game->getGrid()));
    }

    /**
     * @param \App\Models\Game $game
     * @return void
     * @throws \App\Exceptions\GameIsNotNewException
     * @throws \App\Exceptions\GameOverException
     * @throws \App\Exceptions\SquareNotFoundException
     */
    private function flag(): void
    {
        $square = $this->getSquareByCords($this->ask('Choose the row and column to flag. In format ROW,COL'));

        if ($square->isFlagged()) {
            $square->unFlag();
        } else {
            $square->flag();
        }

        $this->output->writeln(ConsoleGridTransformer::transform($this->game->getGrid()));
    }

    /**
     * @param string $prompt
     * @return \App\Square\Interfaces\SquareContract
     * @throws \App\Exceptions\SquareNotFoundException
     */
    private function getSquareByCords(string $prompt): SquareContract
    {
        $cords = explode(',', $prompt);
        return $this->game->getGrid()->getSquareByRowAndCol($cords[0]??0, $cords[1] ?? 0);
    }
}