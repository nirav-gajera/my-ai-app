<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUsersOfPageUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public \App\Models\Page $page)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \App\Models\User::chunk(100, function ($users) {
            foreach ($users as $user) {
                \Illuminate\Support\Facades\Mail::to($user)->send(
                    new \App\Mail\PageUpdatedMail($user, $this->page)
                );
            }
        });
    }
}
