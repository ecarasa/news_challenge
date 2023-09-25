<!DOCTYPE html>
<html>

<head>
    <title>Reservations</title>
</head>

<body>

    <h2>Reservations</h2>


    <input type="text" id="emailSearch" placeholder="search by email">
    <button onclick="performSearch()">Buscar</button>



    <table border="1" id="reservationsTable" style="width: 75%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $reservation)
            <tr>
                <td>{{ $reservation->id }}</td>
                <td>{{ $reservation->full_name }}</td>
                <td>{{ $reservation->email }}</td>
                <td>{{ $reservation->status }}</td>
                <td>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>
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
                        <button onclick="editReservation(${reservation.id})">Edit</button>
                        <button onclick="deleteReservation(${reservation.id})">Delete</button>
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
    </script>
</body>

</html>
