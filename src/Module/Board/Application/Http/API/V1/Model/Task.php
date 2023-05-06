<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Model;

use OpenApi\Attributes as OA;

class Task implements \JsonSerializable
{
    #[OA\Property(description: 'ID of the task', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $id;

    #[OA\Property(description: 'Task title', example: 'Title')]
    private string $title;

    #[OA\Property(description: 'State of the task', example: 'created')]
    private string $state;

    /**
     * @var Comment[]
     */
    private array $comments;

    /**
     * @var TaskLabel[]
     */
    private array $labels;

    public function __construct(string $id, string $title, string $state, array $comments, array $labels)
    {
        $this->id = $id;
        $this->title = $title;
        $this->state = $state;
        $this->comments = $comments;
        $this->labels = $labels;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->state,
            'comments' => array_map(fn(Comment $comment) => $comment->jsonSerialize(), $this->comments),
            'labels' => array_map(fn(TaskLabel $label) => $label->jsonSerialize(), $this->labels)
        ];
    }
}
