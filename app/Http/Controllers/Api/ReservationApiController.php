<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservationApiController extends Controller
{

    public $fixedAmount = 220;

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

            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email',
                'number_of_guests' => 'required|integer|between:1,12',
                'status' => 'required|in:PAID,WITHDRAWN,EXPIRED,CANCELED',
                'reservation_code' => 'required|integer',
                'date' => 'required|date',
                'discount' => 'required|numeric',
                'payment_type' => 'required|string|in:Cash,Credit Card,Online,Debit Card',
            ]);

            $numberOfGuests = $request->input('number_of_guests', 0);
            $discount = $request->input('discount', 0);
            $data['total_amount'] = $this->calculateTotalAmount($numberOfGuests, $discount, $this->fixedAmount);

            // unique id creation and validation
            $data['id'] = $this->createUniqueReservationId();

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

    private function calculateTotalAmount($numberOfGuests, $discount, $amount)
    {
        return ($numberOfGuests * $amount) - $discount;
    }

    public function update(Request $request, Reservation $reservation)
    {
        try {

            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email',
                'number_of_guests' => 'required|integer|between:1,12',
                'status' => 'required|in:PAID,WITHDRAWN,EXPIRED,CANCELED',
                'reservation_code' => 'required|integer',
                'date' => 'required|date',
                'discount' => 'required|numeric',
                'amount' => 'required|numeric',
                'payment_type' => 'required|string|in:Cash,Credit Card,Online,Debit Card',
            ]);

            $numberOfGuests = $request->input('number_of_guests', 0);
            $discount = $request->input('discount', 0);
            $amount = $request->input('amount', $this->fixedAmount);

            $data['total_amount'] = $this->calculateTotalAmount($numberOfGuests, $discount, $amount);

            $reservation->update($data);

            return response()->json($reservation);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json(null, 204);
    }
}
