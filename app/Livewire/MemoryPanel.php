<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class MemoryPanel extends Component
{
    public string $selectedFile = 'MEMORY.md';
    public string $content = '';
    public array $availableFiles = [];
    public bool $fileNotFound = false;

    public function mount(): void
    {
        $this->loadAvailableFiles();
        $this->loadMemoryFile();
    }

    public function loadAvailableFiles(): void
    {
        $this->availableFiles = [];
        $baseDir = base_path('../../workspace');

        // Check for MEMORY.md
        if (File::exists("{$baseDir}/MEMORY.md")) {
            $this->availableFiles[] = [
                'name' => 'MEMORY.md',
                'path' => 'MEMORY.md',
                'group' => 'long-term',
            ];
        }

        // Check for daily memory files
        $memoryDir = "{$baseDir}/memory";
        if (File::isDirectory($memoryDir)) {
            $files = File::files($memoryDir);
            $sorted = collect($files)
                ->map(fn($f) => [
                    'name' => $f->getFilename(),
                    'path' => "memory/{$f->getFilename()}",
                    'group' => 'daily',
                ])
                ->sortByDesc('name')
                ->values()
                ->toArray();
            $this->availableFiles = array_merge($this->availableFiles, $sorted);
        }
    }

    public function selectFile(string $file): void
    {
        $this->selectedFile = $file;
        $this->loadMemoryFile();
    }

    public function loadMemoryFile(): void
    {
        $baseDir = base_path('../../workspace');
        $filePath = "{$baseDir}/{$this->selectedFile}";

        if (File::exists($filePath)) {
            $this->fileNotFound = false;
            $rawContent = File::get($filePath);
            // Truncate to 2000 chars for display
            $this->content = strlen($rawContent) > 2000 
                ? substr($rawContent, 0, 2000) . "\n\n[... truncated ...]"
                : $rawContent;
        } else {
            $this->fileNotFound = true;
            $this->content = '';
        }
    }

    public function render()
    {
        return view('livewire.memory-panel');
    }
}
