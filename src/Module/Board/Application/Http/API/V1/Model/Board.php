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
        description: 'Board display',
        example: 'task'
    )]
    private string $display;

    #[OA\Property(
        description: 'Theme',
        example: 'light'
    )]
    private string $theme;

    /** @var Task[] */
    private array $tasks;

    /**
     * @param Task[] $tasks
     */
    public function __construct(string $id, string $title, string $display, string $theme, array $tasks)
    {
        $this->id = $id;
        $this->title = $title;
        $this->display = $display;
        $this->theme = $theme;
        $this->tasks = $tasks;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'display' => $this->display,
            'theme' => $this->theme,
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

    public function getDisplay(): string
    {
        return $this->display;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }
}
