<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DueDateChange;

class DueDateChangeController extends Controller
{
    /**
     * Motivos de alteração de prazo (tarefas e oportunidades).
     *
     * @return \Illuminate\Http\Response
     */
    public function reasons()
    {
        $reasons = collect(DueDateChange::REASONS)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values();

        return response()->json(['data' => $reasons], 200);
    }
}
