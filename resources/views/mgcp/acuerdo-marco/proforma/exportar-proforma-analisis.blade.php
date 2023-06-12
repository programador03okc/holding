<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        .table ul {
            list-style: none;
            margin-bottom: 0;
        }
        .table ul li {
            margin-left: -40px;
        }
    </style>
</head>
<body>
    <table class="table table-border">
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="15"><b>Fecha Emi.</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Proforma</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Entidad</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Categoria</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Marca</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Producto</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Part Number</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Cantidad</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Fecha Entrega</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Costo</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Comentarios</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Precio</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Total</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Margen</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Resultado</b></th>
                @if (Auth::user()->tieneRol(67))
                <th style="background-color: #ababab;" width="30"><b>Competencia</b></th>
                <th style="background-color: #ababab;" width="20"><b>Modelo</b></th>
                <th style="background-color: #ababab;" width="20"><b>Part Number</b></th>
                <th style="background-color: #ababab;" width="12"><b>Costo</b></th>
                <th style="background-color: #ababab;" width="12"><b>Precio</b></th>
                <th style="background-color: #ababab;" width="12"><b>Margen</b></th>
                @endif
            </tr>
        </thead>
        <tbody>
        @foreach($lista as $item)
            <tr>
                <td>{{ $item['fecha_emision'] }}</td>
                <td>{{ $item['proforma'] }}</td>
                <td>{{ $item['nombre_entidad'] }}</td>
                <td>{{ $item['categoria'] }}</td>
                <td>{{ $item['marca'] }}</td>
                <td>{{ $item['modelo'] }}</td>
                <td>{{ $item['part_no'] }}</td>
                <td>{{ $item['cantidad'] }}</td>
                <td>{{ $item['fin_entrega'] }}</td>
                <td>{{ $item['costo'] }}</td>
                <td>
                    <ul>
                        @foreach ($item['comentarios'] as $comment)
                            <li>{{ $comment->comentario }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>{{ $item['precio_flete'] }}</td>
                <td>{{ $item['total'] }}</td>
                <td>{{ $item['margen'] }}</td>
                <td>{{ $item['resultado'] }}</td>
                @if (Auth::user()->tieneRol(67))
                <td>{{ $item['comp_proveedor'] }}</td>
                <td>{{ $item['comp_modelo'] }}</td>
                <td>{{ $item['comp_part_no'] }}</td>
                <td>{{ $item['comp_costo'] }}</td>
                <td>{{ $item['comp_precio'] }}</td>
                <td>{{ $item['comp_margen'] }}</td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>