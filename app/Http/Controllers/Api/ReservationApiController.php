<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservationApiController extends Controller
{

    public $validationData = [
        'full_name' => 'required|string|max:255',
        'email' => 'required|email',
        'number_of_guests' => 'required|integer|between:1,12',
        'status' => 'required|in:PAID,WITHDRAWN,EXPIRED,CANCELED',
        'reservation_code' => 'required|integer',
        'date' => 'required|date',
        //'amount' => 'required|numeric', -> disabled not traveling to the back and fixed price
        'discount' => 'required|numeric',
        'payment_type' => 'required|string|in:Cash,Credit Card,Online,Debit Card',
    ];

    private function generateReservationId()
    {
        $characters = 'QWERTYUIOPASDFGHJKLZXCVBNM';
        $randomString = '';
        $length = strlen($characters);

        for ($i = 0; $i < 7; $i++) {
            $randomString .= $characters[rand(0, $length - 1)];
        }

        return $randomString;
    }

    private function idExistsInReservations($id)
    {
        return Reservation::where('id', $id)->exists();
    }

    public function createUniqueReservationId()
    {
        $id = $this->generateReservationId();
        while ($this->idExistsInReservations($id)) {
            $id = $this->generateReservationId();
        }
        return $id;
    }

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
        try {
            $data = $request->validate($this->validationData);

            // unique id creation and validation
            $data['id'] = $this->createUniqueReservationId();

            // re calc totals, preventing not being modified in front
            $numberOfGuests = $request->input('number_of_guests', 0);
            $amount = 220;
            $discount = $request->input('discount', 0);
            $totalAmount = ($numberOfGuests * $amount) - $discount;
            $data['total_amount'] = $totalAmount;

            $reservation = Reservation::create($data);

            return response()->json($reservation, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function edit(Reservation $reservation)
    {
        return view('edit', ['reservation' => $reservation]);
    }

    public function update(Request $request, Reservation $reservation)
    {

        $data = $request->validate([]);

        $reservation->update($data);

        return response()->json($reservation);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json(null, 204);
    }
}
