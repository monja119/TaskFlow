<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\Notification\ProjectAtRiskNotificationService;
use Illuminate\Console\Command;

class SendProjectAtRiskNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-project-at-risk
                            {--threshold=70 : Risk score threshold}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for projects at risk';

    public function __construct(
        private readonly ProjectAtRiskNotificationService $projectAtRiskNotification,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $threshold = (int) $this->option('threshold');
        
        $this->info("Looking for projects with risk score > {$threshold}...");

        $projects = Project::query()
            ->with(['users'])
            ->active()
            ->where('risk_score', '>', $threshold)
            ->get();

        if ($projects->isEmpty()) {
            $this->info('No at-risk projects found.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($projects as $project) {
            $this->projectAtRiskNotification->send($project);
            $count++;
            
            $this->line("✓ Sent notification for project: {$project->name} (Risk: {$project->risk_score})");
        }

        $this->info("Successfully sent {$count} project at-risk notifications.");

        return self::SUCCESS;
    }
}
