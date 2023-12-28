<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kalibrator</title>
</head>
<body>
    <table>
        <tr>
            <th style="width: 10px">No</th>
            <th style="width: 600px">Kalibrator</th>
            <th>Jumlah Alkes</th>
        </tr>
        @foreach ($calibrators as $calibrator => $alkesValues)
            <tr>
                <td>
                    {{ $loop->index + 1 }}
                </td>
                <td>
                    {{ $calibrator }}
                </td>
                <td style="text-align: center">
                   {{ count($alkesValues) }}
                </td>
            </tr>
        @endforeach
    </table>
    
</body>
</html>