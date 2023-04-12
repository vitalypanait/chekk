<?php

declare(strict_types=1);

namespace App\Module\Common\Bus;

use League\Tactician\CommandBus as TacticianCommandBus;

class CommandBusImpl implements CommandBus
{
    private TacticianCommandBus $commandBus;

    public function __construct(TacticianCommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function execute(object $command): void
    {
        $this->commandBus->handle($command);
    }
}
