<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AnimalIdentifier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnimalIdentifierController extends Controller
{
    public function store(Request $request, Animal $animal)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'active' => 'boolean',
        ]);

        $animal->identifiers()->create([
            'uid' => Str::uuid(),
            'type' => $validated['type'],
            'code' => $validated['code'],
            'active' => $request->has('active'),
        ]);

        return back()->with('success', 'Identifier added successfully.');
    }

    public function update(Request $request, AnimalIdentifier $animalIdentifier)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'active' => 'boolean',
        ]);

        $animalIdentifier->update([
            'type' => $validated['type'],
            'code' => $validated['code'],
            'active' => $request->has('active'),
        ]);

        return back()->with('success', 'Identifier updated successfully.');
    }

    public function destroy(AnimalIdentifier $animalIdentifier)
    {
        $animalIdentifier->delete();

        return back()->with('success', 'Identifier deleted successfully.');
    }
}
