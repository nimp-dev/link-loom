<?php

namespace Nimp\LinkLoom\CLI;

use Nimp\LinkLoom\CLI\commands\TestCommand;
use Nimp\LinkLoom\CLI\interfaces\CliCommandInterface;

class CommandHandler
{

    /**
     * @var CliCommandInterface[]
     */
    protected array $commands = [];

    protected CliCommandInterface $defaultCommand;

    /**
     * @param CliCommandInterface $defaultCommand
     */
    public function __construct(CliCommandInterface $defaultCommand = new TestCommand())
    {
        $this->defaultCommand = $defaultCommand;
        $this->commands[$defaultCommand::getCommandName()] = $defaultCommand;
    }

    /**
     * @param CliCommandInterface $command
     * @return $this
     */
    public function addCommand(CliCommandInterface $command): self
    {
        $this->commands[$command::getCommandName()] = $command;
        return $this;
    }


    /**
     * @param array $params
     * @param callable|null $callback
     * @return void
     */
    public function handle(array $params = [], ?callable $callback = null): void
    {
        $command = $this->defaultCommand::getCommandName();

        array_splice($params, 0, 1);
        if (!empty($params)) {
            $command = current($params);
            array_splice($params, 0, 1);
        }

        try {
            $service = $this->commands[$command];
            $service->run($params);
        } catch (\Exception $e) {
            if ($callback) {
                $callback($params, $e);
            }
        }
    }

}