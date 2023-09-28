<!DOCTYPE html>
<html>

<head>
    <title>Upload CSV</title>
    <link rel="stylesheet" href="{{ URL::asset('css/styles.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <h2>Upload CSV Reservations</h2>

    <form action="{{ route('store.csv') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="csv_file" accept=".csv" required>
        <button type="submit">Upload</button>
    </form>

    @if (isset($errors) && $errors->has('error_controller'))
    <div class="alert_danger_card">
        {{ $errors->first('error_controller') }}
    </div>
    @endif

    @if (isset($csvErrors) && count($csvErrors) > 0)
    <h2>CSV Errors</h2>
    <div class="err_list">
        @foreach ($csvErrors as $error)
        <div class="err_csv">
            <p>
                Line: {{ $error['line'] }}<br>
                Row: {{ implode(';', $error['row']) }}<br>
                Messages:
            </p>
            <ul>
                @foreach ($error['messages'] as $message)
                <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
    @endif

</body>

</html>