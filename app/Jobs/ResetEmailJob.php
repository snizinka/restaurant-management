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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ResetEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $password;
    public function __construct(User $user, $password)
    {
        $this->data = $user;
        $this->password = $password;
    }
    public function handle(): void
    {
        Mail::to($this->data->email)->send(new ResetEmail($this->data->id, $this->password));
    }
}
