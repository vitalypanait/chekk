<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Model;

use OpenApi\Attributes as OA;

class Board implements \JsonSerializable
{
    #[OA\Property(
        description: 'ID of board',
        example: '839cf68e-4062-4259-addc-09ce5644ee52'
    )]
    private string $id;

    #[OA\Property(
        description: 'Title',
        example: 'Let\'s start'
    )]
    private string $title;

    #[OA\Property(
        description: 'Board type',
        example: 'task'
    )]
    private string $type;

    /** @var Task[] */
    private array $tasks;

    /**
     * @param Task[] $tasks
     */
    public function __construct(string $id, string $title, array $tasks)
    {
        $this->id = $id;
        $this->title = $title;
        $this->tasks = $tasks;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'tasks' => array_map(fn(Task $task) => $task->jsonSerialize(), $this->tasks)
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }
}
