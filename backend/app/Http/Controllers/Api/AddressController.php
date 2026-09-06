<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->addresses()->latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $address = $request->user()->addresses()->create($this->validated($request));
        if ($address->is_default || $request->user()->addresses()->count() === 1) $this->makeDefault($address);
        return response()->json(['data' => $address->fresh()], 201);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        abort_unless($address->user_id === $request->user()->id, 404);
        $address->update($this->validated($request));
        if ($address->is_default) $this->makeDefault($address);
        return response()->json(['data' => $address->fresh()]);
    }

    public function destroy(Request $request, Address $address): JsonResponse
    {
        abort_unless($address->user_id === $request->user()->id, 404);
        $wasDefault = $address->is_default;
        $address->delete();
        if ($wasDefault) $request->user()->addresses()->latest()->first()?->update(['is_default' => true]);
        return response()->json(['message' => 'Domicilio eliminado.']);
    }

    public function makeDefaultRoute(Request $request, Address $address): JsonResponse
    {
        abort_unless($address->user_id === $request->user()->id, 404);
        $this->makeDefault($address);
        return response()->json(['data' => $address->fresh()]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:80'], 'recipient' => ['required', 'string', 'max:120'],
            'line1' => ['required', 'string', 'max:180'], 'line2' => ['nullable', 'string', 'max:180'],
            'city' => ['required', 'string', 'max:100'], 'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'regex:/^\d{5}$/'], 'phone' => ['nullable', 'string', 'max:30'],
            'is_default' => ['sometimes', 'boolean'],
        ]);
    }

    private function makeDefault(Address $address): void
    {
        DB::transaction(function () use ($address): void {
            Address::where('user_id', $address->user_id)->whereKeyNot($address->id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });
    }
}
