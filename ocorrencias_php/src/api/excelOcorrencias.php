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
            <th>OCORRENCIA</th>
            <th>MOTIVO</th>
            <th>SUBMOTIVO</th>
            <th>DATA</th>
            <th>DATA ATEND.</th>
            <th>STATUS</th>
            
        </tr>
        <tbody>
            <?php foreach($data->data as $key => $value): ?>
                <tr>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->LOJA) ?></td>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->OCORRENCIA) ?></td>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->MOTIVO) ?></td>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->SUBMOTIVO) ?></td>
                    <td><?=date('d/m/Y', strtotime(iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->DATA))) ?></td>
                    <td><?= $data->data[$key]->DATA_ATEND ? date('d/m/Y', strtotime(iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->DATA_ATEND))) : ""?></td>
                    <td><?=iconv('UTF-8', 'ISO-8859-1', $data->data[$key]->STATUS) ?></td>
                    
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    
</body>
</html>
