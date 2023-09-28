<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;



class ReservationController extends Controller
{

    private $csv_delimiter = ";";

    public function index(Request $request)
    {
        $reservations = Reservation::query();

        if ($request->has('email') && $request->email) {
            $reservations->where('email', 'like', '%' . $request->email . '%');
        }

        $reservations = $reservations->orderBy('created_at', 'desc')->get();

        return view('reservations.index', ['reservations' => $reservations]);
    }

    public function storeCSV(Request $request)
    {

        $validatedData = $request->validate(['csv_file' => 'required|mimes:csv,txt']);
        $path = $request->file('csv_file')->getRealPath();
        $data = array_map('str_getcsv', file($path));

        if (count($data) <= 1) {
            return redirect()->back()->withErrors(['error_controller' => 'CSV is empty']);
        }

        $header = explode($this->csv_delimiter, $data[0][0]);
        unset($data[0]);

        $errors = [];

        foreach ($data as $index => $row) {
            $rowArr = explode($this->csv_delimiter, $row[0]);

            if (count($rowArr) !== count($header)) {
                $errors[] = [
                    'line' => $index + 2,
                    'row' => $row,
                    'messages' => ["the row doesnt' match"]
                ];
                continue;
            }

            $reservationData = array_combine([
                'id',
                'full_name',
                'email',
                'number_of_guests',
                'status',
                'reservation_code',
                'date',
                'amount',
                'discount',
                'total_amount',
                'payment_type'
            ], $rowArr);


            $validator = Validator::make($reservationData, [
                'id' => 'required|string',
                'full_name' => 'required|string',
                'email' => 'required|email',
                'number_of_guests' => 'required|integer|between:1,12',
                'status' => 'required|in:PAID,WITHDRAWN,EXPIRED,CANCELED',
                'reservation_code' => 'required|integer',
                'date' => 'required|date',
                'amount' => 'required|numeric',
                'discount' => 'required|numeric',
                'payment_type' => 'required|in:Cash,Credit Card,Online,Debit Card',
            ]);


            if ($validator->fails()) {
                $errors[] = [
                    'line' => $index + 2,
                    'row' => $rowArr,
                    'messages' => $validator->messages()->all()
                ];
            } else {
                Reservation::updateOrCreate($reservationData);
            }
        }

        if (!empty($errors)) {
            return view('reservations.upload')->with('csvErrors', $errors);
        }

        return redirect()->route('reservations.index')->with('success', 'CSV imported succesfully.');
    }

    public function uploadForm()
    {
        return view('reservations.upload');
    }

    public function edit($id)
    {
        $reservation = Reservation::findOrFail($id);

        return view('reservations.edit_reservation', ['reservation' => $reservation]);
    }

    public function create()
    {
        return view('reservations.create_reservation');
    }
}
