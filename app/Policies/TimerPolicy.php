<?php

namespace App\Policies;

use App\User;
use App\Timer;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimerPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Timer $timer)
    {
        return $user->id === $timer->user_id;
    }

    public function delete(User $user, Timer $timer)
    {
        return $user->id === $timer->user_id;
    }
}
