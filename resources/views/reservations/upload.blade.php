<!DOCTYPE html>
<html>

<head>
    <title>Upload CSV</title>
</head>

<body>

    <form action="{{ route('store.csv') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="csv_file" accept=".csv">
        <button type="submit">Upload</button>
    </form>

    @if (isset($errors) && count($errors) > 0)
    <h2>Errores</h2>
    @foreach ($errors as $error)
    <p>
        <!-- Fila: {{ implode(';', $error['row']) }}<br> -->
        <!-- Mensajes: <br> -->
    <ul>
        @foreach ($error['messages']->all() as $message)
        <li>{{ $message }}</li>
        @endforeach
    </ul>
    </p>
    @endforeach
    @endif


</body>

</html>
