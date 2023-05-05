<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Entity\Label;

interface LabelRepository
{
    public function save(Label $label): void;

    public function delete(Label $label): void;

    public function getById(string $id): Label;

    /**
     * @return Label[]
     */
    public function findByBoard(Board $board): array;
}
