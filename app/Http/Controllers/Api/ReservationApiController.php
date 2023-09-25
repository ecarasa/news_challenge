<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationApiController extends Controller
{

    public function index(Request $request)
    {
        $query = Reservation::query();

        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        return $query->get();
    }


    public function show(Reservation $reservation)
    {
        return $reservation;
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            // ... tus reglas de validación aquí ...
        ]);

        $reservation = Reservation::create($data);

        return response()->json($reservation, 201);
    }


    public function update(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            // ... tus reglas de validación aquí ...
        ]);

        $reservation->update($data);

        return response()->json($reservation);
    }


    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json(null, 204);
    }
}
