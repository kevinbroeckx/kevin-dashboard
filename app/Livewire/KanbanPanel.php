<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class KanbanPanel extends Component
{
    public array $columns = [];
    public string $newTask = '';
    public string $newTaskColumn = 'todo';
    public ?string $selectedTaskId = null;
    public ?string $selectedTaskColumn = null;
    public string $editTitle = '';
    public string $editDescription = '';
    public string $editGoal = '';
    public string $editPriority = 'medium';

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
            'description' => '',
            'goal' => '',
            'priority' => 'medium',
            'created' => now()->toIso8601String(),
        ];

        $this->newTask = '';
        $this->saveTasks();
    }

    public function openTask(string $taskId, string $column): void
    {
        $this->selectedTaskId = $taskId;
        $this->selectedTaskColumn = $column;
        
        foreach ($this->columns[$column] as $task) {
            if ($task['id'] === $taskId) {
                $this->editTitle = $task['title'];
                $this->editDescription = $task['description'] ?? '';
                $this->editGoal = $task['goal'] ?? '';
                $this->editPriority = $task['priority'] ?? 'medium';
                break;
            }
        }
    }

    public function closeTask(): void
    {
        $this->selectedTaskId = null;
        $this->selectedTaskColumn = null;
        $this->resetEdit();
    }

    public function saveTask(): void
    {
        if (!$this->selectedTaskId || !$this->selectedTaskColumn) return;

        foreach ($this->columns[$this->selectedTaskColumn] as &$task) {
            if ($task['id'] === $this->selectedTaskId) {
                $task['title'] = $this->editTitle;
                $task['description'] = $this->editDescription;
                $task['goal'] = $this->editGoal;
                $task['priority'] = $this->editPriority;
                break;
            }
        }

        $this->saveTasks();
        $this->closeTask();
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
        if ($this->selectedTaskId === $taskId) {
            $this->closeTask();
        }
        $this->saveTasks();
    }

    protected function resetEdit(): void
    {
        $this->editTitle = '';
        $this->editDescription = '';
        $this->editGoal = '';
        $this->editPriority = 'medium';
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
                ['id' => uniqid(), 'title' => 'Wire OpenClaw status API', 'description' => 'Connect status panel to real gateway API', 'goal' => 'Live status updates', 'priority' => 'high', 'created' => now()->toIso8601String()],
                ['id' => uniqid(), 'title' => 'Real-time activity feed', 'description' => 'Build activity log for user actions', 'goal' => 'Track all dashboard interactions', 'priority' => 'medium', 'created' => now()->toIso8601String()],
                ['id' => uniqid(), 'title' => 'Messenger → OpenClaw sessions', 'description' => 'Integrate messaging with session management', 'goal' => 'Send messages to active sessions', 'priority' => 'medium', 'created' => now()->toIso8601String()],
            ],
            'doing' => [
                ['id' => uniqid(), 'title' => 'Dashboard UI layout', 'description' => 'Finalize responsive grid and spacing', 'goal' => 'Production-ready UI', 'priority' => 'high', 'created' => now()->toIso8601String()],
            ],
            'done' => [
                ['id' => uniqid(), 'title' => 'Laravel project setup', 'description' => 'Initialize Laravel 12 with Livewire', 'goal' => 'Project foundation', 'priority' => 'high', 'created' => now()->toIso8601String()],
                ['id' => uniqid(), 'title' => 'Livewire installed', 'description' => 'Configure Livewire 4 components', 'goal' => 'Real-time reactivity', 'priority' => 'high', 'created' => now()->toIso8601String()],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.kanban-panel');
    }
}
