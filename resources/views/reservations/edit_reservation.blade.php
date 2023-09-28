<!DOCTYPE html>
<html>

<head>
    <title>Edit Reservation</title>
    <link rel="stylesheet" href="{{ URL::asset('css/styles.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Esto es lo esencial para diseño responsivo -->

</head>

<body>
    <h2>Edit Reservation "{{$reservation->id}}"</h2>

    <form id="reservationForm" action="/api/reservations/{{ $reservation->id }}" method="POST">
        @csrf
        <input type="hidden" name="_method" value="PUT">

        <div>
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" id="full_name" required value="{{$reservation->full_name}}">
        </div>

        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required value="{{$reservation->email}}">
        </div>

        <div>
            <label for="number_of_guests">Number of Guests:</label>
            <input type="number" name="number_of_guests" id="number_of_guests" min="1" max="12" required onchange="javascript:updateTotalAmount();" value="{{$reservation->number_of_guests}}">
        </div>

        <div>
            <label for="reservation_id">Reservation Code:</label>
            <input type="number" name="reservation_code" id="reservation_code" required value="{{$reservation->reservation_code}}">
        </div>

        <div>
            <label for="date">Date:</label>
            <input type="date" name="date" id="date" required value="{{$reservation->date}}">
        </div>

        <div>
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" value="220" disabled value="{{$reservation->amount}}">
        </div>

        <div>
            <label for="discount">Discount:</label>
            <input type="number" name="discount" id="discount" required onchange="javascript:updateTotalAmount();" value="{{$reservation->discount}}">
        </div>

        <div>
            <label for="total_amount">Total Amount:</label>
            <input type="number" name="total_amount" id="total_amount" disabled value="{{$reservation->total_amount}}">
        </div>
        <div>
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="PAID" {{ $reservation->status == 'PAID' ? 'selected' : '' }}>PAID</option>
                <option value="WITHDRAWN" {{ $reservation->status == 'WITHDRAWN' ? 'selected' : '' }}>WITHDRAWN</option>
                <option value="EXPIRED" {{ $reservation->status == 'EXPIRED' ? 'selected' : '' }}>EXPIRED</option>
                <option value="CANCELED" {{ $reservation->status == 'CANCELED' ? 'selected' : '' }}>CANCELED</option>
                <option value="RETURNED" {{ $reservation->status == 'RETURNED' ? 'selected' : '' }}>RETURNED</option>
            </select>
        </div>

        <div>
            <label for="payment_type">Payment Type:</label>
            <select name="payment_type" id="payment_type" required>
                <option value="Cash" {{ $reservation->payment_type == 'Cash' ? 'selected' : '' }}>Cash</option>
                <option value="Credit Card" {{ $reservation->payment_type == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                <option value="Online" {{ $reservation->payment_type == 'Online' ? 'selected' : '' }}>Online</option>
                <option value="Debit Card" {{ $reservation->payment_type == 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
            </select>
        </div>
        <div id="ajax_result">

        </div>
        <div>
            <button type="submit">Edit Reservation</button>
            <a class="btn btndefault" href="{{route('reservations.index')}}">Back to Reservations</a>
        </div>
    </form>


    <script>
        const form = document.getElementById('reservationForm');
        const alertContainerElement = document.getElementById('ajax_result');

        document.getElementById('number_of_guests').addEventListener('input', updateTotalAmount);
        document.getElementById('discount').addEventListener('input', updateTotalAmount);

        function updateTotalAmount() {
            const numberOfGuests = parseFloat(document.getElementById('number_of_guests').value) || 0;
            const amount = 220;
            const discount = parseFloat(document.getElementById('discount').value) || 0;

            const totalAmount = (numberOfGuests * amount) - discount;
            document.getElementById('total_amount').value = totalAmount.toFixed(2);
        }

        updateTotalAmount();

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw err;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    alertContainerElement.innerHTML = `<div style="margin-top: 20px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px;">Reservation updated successfully for ${data.full_name} with ID: ${data.id}.</div>`;
                })
                .catch(error => {
                    if (error.errors) {
                        const alertContainerElement = document.getElementById('ajax_result');
                        let errorMessages = '<ul class="err-alert">';

                        for (const field in error.errors) {
                            errorMessages += '<li>' + error.errors[field][0] + '</li>';
                        }

                        errorMessages += '</ul>';
                        alertContainerElement.innerHTML = errorMessages;
                    } else {
                        console.error(error);
                        alert('There was a problem with the request.');
                    }
                });
        });
    </script>

</body>

</html>