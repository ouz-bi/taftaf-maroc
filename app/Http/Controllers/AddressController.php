<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'instructions' => 'nullable|string|max:500',
            'is_default' => 'boolean'
        ]);

        // Si nouvelle adresse par défaut, retirer le défaut des autres
        if ($request->is_default) {
            Address::where('user_id', auth()->id())
                ->update(['is_default' => false]);
        }

        $address = Address::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Adresse ajoutée avec succès');
    }

    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'instructions' => 'nullable|string|max:500',
            'is_default' => 'boolean'
        ]);

        if ($request->is_default) {
            Address::where('user_id', auth()->id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return back()->with('success', 'Adresse mise à jour');
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        $address->delete();

        return back()->with('success', 'Adresse supprimée');
    }
}
