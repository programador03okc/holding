<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" align="center" rowspan="2" width="15">Fecha</th>
                <th style="background-color: #cccccc;" align="center" rowspan="2" width="30">Entidad</th>
                <th style="background-color: #cccccc;" align="center" rowspan="2">Cantidad</th>
                <th style="background-color: #cccccc;" align="center" colspan="6">Empresa</th>
                <th style="background-color: #cccccc;" align="center" colspan="6">Proveedor (Competencia)</th>
            </tr>
            <tr>
                <th style="background-color: #cccccc;" align="center" width="15">Razón Social</th>
                <th style="background-color: #cccccc;" align="center" width="40">Ficha Producto</th>
                <th style="background-color: #cccccc;" align="center" width="12">Costo</th>
                <th style="background-color: #cccccc;" align="center" width="12">Precio Unit (USD</th>
                <th style="background-color: #cccccc;" align="center" width="12">Total</th>
                <th style="background-color: #cccccc;" align="center" width="12">Margen</th>
                
                <th style="background-color: #cccccc;" align="center" width="20">Razón Social</th>
                <th style="background-color: #cccccc;" align="center" width="40">Ficha Producto</th>
                <th style="background-color: #cccccc;" align="center" width="12">Costo</th>
                <th style="background-color: #cccccc;" align="center" width="12">Precio Unit (USD</th>
                <th style="background-color: #cccccc;" align="center" width="12">Total</th>
                <th style="background-color: #cccccc;" align="center" width="12">Margen</th>
            </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr>
                <td>{{ date('d-m-Y', strtotime($item['fecha'])) }}</td>
                <td>{{ $item['entidad'] }}</td>
                <td>{{ $item['cantidad'] }}</td>
                <td style="background-color: #ffffb0;">{{ $item['empresa'] }}</td>
                <td style="background-color: #ffffb0;">{{ $item['producto'] }}</td>
                <td style="background-color: #ffffb0;">{{ $item['costo'] }}</td>
                <td style="background-color: #ffffb0;">{{ $item['precio_uni'] }}</td>
                <td style="background-color: #ffffb0;">{{ $item['total'] }}</td>
                <td style="background-color: #ffffb0;">{{ $item['margen'] }}</td>
                <td style="background-color: #ffb0b0;">{{ $item['entidad'] }}</td>
                <td style="background-color: #ffb0b0;">{{ $item['producto_ext'] }}</td>
                <td style="background-color: #ffb0b0;">{{ $item['costo_ext'] }}</td>
                <td style="background-color: #ffb0b0;">{{ $item['precio_uni_ext'] }}</td>
                <td style="background-color: #ffb0b0;">{{ $item['total_ext'] }}</td>
                <td style="background-color: #ffb0b0;">{{ $item['margen_ext'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>