<?php

namespace App\Traits;

use Inertia\Inertia;

trait FlashMessage
{
    public function flashToast(string $type, string $message): void
    {
        Inertia::flash('toast', [
            'type' => $type,
            'message' => $message,
        ]);
    }

    public function flashSuccess(string $message): void
    {
        $this->flashToast('success', $message);
    }

    public function flashError(string $message): void
    {
        $this->flashToast('error', $message);
    }
}
