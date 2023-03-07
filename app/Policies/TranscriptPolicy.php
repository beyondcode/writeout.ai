<?php

namespace App\Policies;

use App\Models\Transcript;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TranscriptPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(?User $user, Transcript $transcript): bool
    {
        return $user?->id === $transcript->user_id || $transcript->public;
    }

    public function create(User $user): bool
    {
        return true;
    }
}
