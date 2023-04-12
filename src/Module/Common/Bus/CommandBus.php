<?php

declare(strict_types=1);

namespace App\Module\Common\Bus;

interface CommandBus
{
    /**
     * @param object $command
     */
    public function execute(object $command): void;
}
