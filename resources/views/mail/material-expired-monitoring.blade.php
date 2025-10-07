<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EMAIL</title>
</head>
<body style="padding: 0;margin:0; font-family: Arial, Helvetica, sans-serif; color: #000">
    <h1 style="font-size: 24px; text-align: center">Material Expired Monitoring</h1>
    <table style="width:500px; margin: 0px auto" aria-colspan="" cellpadding="0" cellspacing="0" style="background-color:#F9F9F9;">
        <thead>
            <tr style="background-color: @if($status == 'expired') red @elseif($status == 'warning1') yellow @elseif($status == 'warning2') orange @else white @endif">
                <th colspan="4" style="padding: 5px; border: solid #333; border-width: 1px 1px 0 1px">ALERT @if($status == 'expired') 3 @elseif($status == 'warning1') 1 @elseif($status == 'warning2') 2 @else 0 @endif !!</th>
            </tr>
            <tr style="text-align: left">
                <th colspan="4" style="padding: 5px; border: solid #333; border-width: 1px 1px 0 1px"></th>
            </tr>
            <tr style="background-color: @if($status == 'expired') red @elseif($status == 'warning1') yellow @elseif($status == 'warning2') orange @else white @endif; text-align: left">
                <th style="padding: 5px; border: solid #333; border-width: 1px 0 0 1px">Material Type</th>
                <th style="padding: 5px; border: solid #333; border-width: 1px 0 0 1px">SLOC</th>
                <th style="padding: 5px; border: solid #333; border-width: 1px 0 0 1px">BUn</th>
                <th style="padding: 5px; border: solid #333; border-width: 1px 1px 0 1px">Sum of QTY</th>
            </tr>
        </thead>
        <tbody>
            {{-- {{ dd($materials) }} --}}
            @foreach ($materials as $_material)
                @foreach ($_material as $material)
                    <tr>
                        <td style="padding: 5px; border: solid #333; border-width: 1px 0 0 1px">{{ $material['type'] }}</td>
                        <td style="padding: 5px; border: solid #333; border-width: 1px 0 0 1px">{{ $material['sloc'] }}</td>
                        <td style="padding: 5px; border: solid #333; border-width: 1px 0 0 1px">{{ $material['uom'] }}</td>
                        <td style="padding: 5px; border: solid #333; border-width: 1px 1px 0 1px; text-align: right">{{ number_format($material['sum'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: @if($status == 'expired') red @elseif($status == 'warning1') yellow @elseif($status == 'warning2') orange @else white @endif; text-align: left">
                <th colspan="3" style="padding: 5px; border: solid #333; border-width: 1px 0 1px 1px">GRAND TOTAL</th>
                <th style="padding: 5px; border: solid #333; border-width: 1px 1px 1px 1px; text-align: right">{{ number_format($grand_total, 2, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
