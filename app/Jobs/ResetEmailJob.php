<?php

namespace App\Jobs;

use App\Mail\ResetEmail;
use App\Mail\WelcomeUser;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ResetEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    public function __construct(User $user)
    {
        $this->data = $user;
    }
    public function handle(): void
    {
        Mail::to($this->data->email)->send(new ResetEmail($this->data->id));
    }
}
