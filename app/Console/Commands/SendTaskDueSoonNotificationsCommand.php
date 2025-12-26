<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Enums\TaskStatus;
use App\Services\Notification\TaskDueSoonNotificationService;
use Illuminate\Console\Command;

class SendTaskDueSoonNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-task-due-soon
                            {--days=3 : Number of days before due date to send notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for tasks that are due soon';

    public function __construct(
        private readonly TaskDueSoonNotificationService $taskDueSoonNotification,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        
        $this->info("Looking for tasks due in {$days} days...");

        $tasks = Task::query()
            ->with(['users', 'project'])
            ->whereNotNull('due_date')
            ->where('status', '!=', TaskStatus::COMPLETED)
            ->whereDate('due_date', '<=', now()->addDays($days))
            ->whereDate('due_date', '>=', now())
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('No tasks due soon found.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($tasks as $task) {
            if ($task->users->isEmpty()) {
                $this->warn("Task #{$task->id} has no assigned users. Skipping...");
                continue;
            }

            $this->taskDueSoonNotification->send($task);
            $count++;
            
            $this->line("✓ Sent notification for task: {$task->title} (Due: {$task->due_date->format('Y-m-d')})");
        }

        $this->info("Successfully sent {$count} task due soon notifications.");

        return self::SUCCESS;
    }
}
