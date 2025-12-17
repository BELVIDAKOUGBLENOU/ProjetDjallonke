<?php

namespace App\Http\Controllers;

use App\Models\Premise;
use App\Models\PremisesKeeper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PremisesKeeperController extends Controller
{
    public function store(Request $request, Premise $premise)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:persons,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $premise->keepers()->create($validated);

        return back()->with('success', 'Keeper added successfully.');
    }

    public function update(Request $request, PremisesKeeper $premisesKeeper)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:persons,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $premisesKeeper->update($validated);

        return back()->with('success', 'Keeper updated successfully.');
    }

    public function destroy(PremisesKeeper $premisesKeeper)
    {
        $premisesKeeper->delete();

        return back()->with('success', 'Keeper removed successfully.');
    }
}
