<?php

namespace App\Console\Commands;

use App\Models\NotebookSnapshot;
use App\Services\NotebookService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\OutputInterface;

class NotebookManage extends Command
{
    protected $signature = 'notebook:manage
        {action : import|export}
        {--file= : Snapshot file to import (- reads stdin, which needs --stamp)}
        {--stamp= : Snapshot stamp, YYYY-MM-DD-HHMM; defaults to the file name}
        {--prepared : The file is a published copy (from just notebook-to-prod): stored as it is}
        {--force : Replace a snapshot already stored}';

    protected $description = 'Publish factory notebook snapshots: import a factory original (or a prepared copy), export a stored one';

    public function __construct(private NotebookService $notebookService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        return match ($this->argument('action')) {
            'import' => $this->import(),
            'export' => $this->export(),
            default  => $this->abort('Unknown action. Use: import or export.'),
        };
    }

    private function import(): int
    {
        $file = $this->option('file');
        $stamp = $this->option('stamp') ?? ($file && $file !== '-' ? basename($file, '.html') : null);

        // Checked before stdin is read, so a bad call never waits on input
        if (! $stamp || ! preg_match(NotebookSnapshot::STAMP, $stamp)) {
            return $this->abort('A stamp (YYYY-MM-DD-HHMM) is required: name the file <stamp>.html or pass --stamp.');
        }

        if ($file === '-') {
            $raw = (string) stream_get_contents(STDIN);
        } elseif ($file && is_file($file)) {
            $raw = file_get_contents($file);
        } else {
            return $this->abort('--file is required and must exist (or - for stdin).');
        }

        if ($this->notebookService->find($stamp) && ! $this->option('force')) {
            return $this->abort("Snapshot {$stamp} is already stored, and replacing it changes a live page. Pass --force if that is intended.");
        }

        $copy = $this->option('prepared') ? ['html' => $raw] : $this->notebookService->prepare($stamp, $raw);

        if ($problems = $this->notebookService->problems($stamp, $copy['html'])) {
            foreach ($problems as $problem) {
                $this->error($problem);
            }

            return 1;
        }

        $this->notebookService->store($stamp, $copy['html']);

        $this->info("Stored snapshot {$stamp}: /notebook/{$stamp}/");

        if (! $this->option('prepared')) {
            $this->line("  Local file links unwrapped: {$copy['unwrapped']}");
            $this->line("  Snapshot links pointed at their page: {$copy['pointed']}");
            $this->line("  Links to unpublished snapshots dropped: {$copy['dropped']}");
        }

        return 0;
    }

    private function export(): int
    {
        $snapshot = $this->notebookService->find((string) $this->option('stamp'));

        // On stderr, so a failed export never travels down the pipe as a page
        if (! $snapshot) {
            $this->output->getErrorStyle()->writeln('<error>Snapshot not found here: ' . $this->option('stamp') . '. Import it first: php artisan notebook:manage import --file=<stamp>.html</error>');
            return 1;
        }

        // The page alone on stdout, so it can be piped to production unchanged
        $this->output->write($snapshot->html, false, OutputInterface::OUTPUT_RAW);

        return 0;
    }

    private function abort(string $message): int
    {
        $this->error($message);

        return 1;
    }
}
