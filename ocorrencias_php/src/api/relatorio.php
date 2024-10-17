<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    table tr td,
    table tr th {
        font-size: 16px;
        line-height: 16px;
        text-align: center;
	}
	
	table tr:nht-child(even) {
	    background-color: #555;
	}
    </style>
</head>
<body>
    <table>
        <tr>
            <th>LOJA</th>
            <th>SERVICE_TAG</th>
            <th>ASSET_TAG</th>
            <th>FABRICANTE</th>
            <th>MODELO</th>
            <th>STATUS</th>
            
        </tr>
        <tbody>
            <?php foreach($data->data as $key => $value): ?>
                <tr>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->LOJA) ?></td>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->SERVICE_TAG) ?></td>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->ASSET_TAG) ?></td>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->FABRICANTE) ?></td>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->MODELO) ?></td>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->STATUS) ?></td>
                    
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    
</body>
</html>
