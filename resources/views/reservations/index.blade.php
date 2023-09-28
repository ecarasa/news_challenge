<!DOCTYPE html>
<html>

<head>
    <title>Reservations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ URL::asset('css/styles.css') }}">
</head>

<body>

    <h2>Reservations</h2>

    <div class="search_container">
        <input type="text" id="emailSearch" placeholder="search by email">
        <button onclick="performSearch()">Buscar</button>
        <a href="{{route('reservations.create')}}" class="btn">New</a>
        <a href="{{route('store.csv')}}" class="btn btndefault">Upload</a>
    </div>

    <div id="ajax_result"></div>
    @if (session('success'))
    <div class="alert_success_card">
        {{ session('success') }}
    </div>
    @endif

    <table border="1" id="reservationsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $reservation)
            <tr id="reservation_row_id_{{ $reservation->id }}">
                <td>{{ $reservation->id }}</td>
                <td>{{ $reservation->full_name }}</td>
                <td>{{ $reservation->email }}</td>
                <td>{{ $reservation->status }}</td>
                <td style="text-align: center;">
                    <a class="btn" href="{{ route('reservations.edit',[$reservation->id]) }}">Edit</a>
                    <button onclick="deleteReservation('{{$reservation->id }}');">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>
        const alertContainerElement = document.getElementById('ajax_result');

        function fetchReservations(email = null) {
            let apiUrl = '/api/reservations';
            if (email) {
                apiUrl += '?email=' + encodeURIComponent(email);
            }

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {

                    const tableBody = document.getElementById('reservationsTable').querySelector('tbody');
                    tableBody.innerHTML = '';

                    data.forEach(reservation => {
                        const row = document.createElement('tr');

                        row.innerHTML = `
                    <td>${reservation.id}</td>
                    <td>${reservation.full_name}</td>
                    <td>${reservation.email}</td>
                    <td>${reservation.status}</td>
                    <td>
                        <button onclick="editReservation('${reservation.id}')">Edit</button>
                        <button onclick="deleteReservation('${reservation.id}')">Delete</button>
                    </td>`;

                        tableBody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function performSearch() {
            const email = document.getElementById('emailSearch').value;
            fetchReservations(email);
        }

        function deleteReservation(reservationId) {

            if (confirm(`Confirm deletion of reservation with ID ${reservationId}?`)) {
                fetch(`/api/reservations/${reservationId}`, {
                        method: 'DELETE'
                    })
                    .then(response => {
                        if (response.ok) {
                            //alert('Reserva eliminada con éxito');
                            const rowToDelete = document.getElementById('reservation_row_id_' + reservationId);
                            rowToDelete.remove();
                            alertContainerElement.innerHTML = `<div class="alert_danger_card">Reservation DELETED successfully with ID: ${reservationId}</div>`;
                        } else {
                            alert('Hubo un error al eliminar la reserva');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>

</body>

</html>