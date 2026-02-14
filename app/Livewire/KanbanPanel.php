<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class KanbanPanel extends Component
{
    public array $columns = [];
    public string $newTask = '';
    public string $newTaskColumn = 'todo';

    protected string $tasksFile;

    public function boot(): void
    {
        $this->tasksFile = storage_path('app/tasks.json');
    }

    public function mount(): void
    {
        $this->loadTasks();
    }

    public function loadTasks(): void
    {
        if (File::exists($this->tasksFile)) {
            $this->columns = json_decode(File::get($this->tasksFile), true) ?? $this->defaultColumns();
        } else {
            $this->columns = $this->defaultColumns();
            $this->saveTasks();
        }
    }

    public function addTask(): void
    {
        if (empty(trim($this->newTask))) return;

        $this->columns[$this->newTaskColumn][] = [
            'id' => uniqid(),
            'title' => $this->newTask,
            'created' => now()->toIso8601String(),
        ];

        $this->newTask = '';
        $this->saveTasks();
    }

    public function moveTask(string $taskId, string $fromCol, string $toCol): void
    {
        foreach ($this->columns[$fromCol] as $i => $task) {
            if ($task['id'] === $taskId) {
                unset($this->columns[$fromCol][$i]);
                $this->columns[$fromCol] = array_values($this->columns[$fromCol]);
                $this->columns[$toCol][] = $task;
                break;
            }
        }
        $this->saveTasks();
    }

    public function removeTask(string $taskId, string $column): void
    {
        $this->columns[$column] = array_values(
            array_filter($this->columns[$column], fn($t) => $t['id'] !== $taskId)
        );
        $this->saveTasks();
    }

    protected function saveTasks(): void
    {
        File::ensureDirectoryExists(dirname($this->tasksFile));
        File::put($this->tasksFile, json_encode($this->columns, JSON_PRETTY_PRINT));
    }

    protected function defaultColumns(): array
    {
        return [
            'todo' => [
                ['id' => uniqid(), 'title' => 'Wire OpenClaw status API', 'created' => now()->toIso8601String()],
                ['id' => uniqid(), 'title' => 'Real-time activity feed', 'created' => now()->toIso8601String()],
                ['id' => uniqid(), 'title' => 'Messenger → OpenClaw sessions', 'created' => now()->toIso8601String()],
            ],
            'doing' => [
                ['id' => uniqid(), 'title' => 'Dashboard UI layout', 'created' => now()->toIso8601String()],
            ],
            'done' => [
                ['id' => uniqid(), 'title' => 'Laravel project setup', 'created' => now()->toIso8601String()],
                ['id' => uniqid(), 'title' => 'Livewire installed', 'created' => now()->toIso8601String()],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.kanban-panel');
    }
}
