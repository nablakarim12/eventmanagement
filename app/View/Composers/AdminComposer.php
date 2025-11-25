<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\EventOrganizer;

class AdminComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $pendingOrganizers = EventOrganizer::where('status', 'pending')->count();
        
        $view->with('pendingOrganizers', $pendingOrganizers);
    }
}