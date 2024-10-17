<?php

// require_once 'vendor/autoload.php';
// require_once '../../vendor/autoload.php';
require_once realpath('../../vendor/autoload.php');

// require_once __DIR__ . '/vendor/autoload.php';

header('Content-type: octet/stream');
// header('Content-type: text/html');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: X-'Request'ed-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
header('Pragma: no-cache');
header('Cache: no-cache');
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0', FALSE);
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Origin: *");

date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt-BR', 'pt-BR.utf-8', 'portuguese');

error_reporting(E_ALL ^ E_NOTICE);

// caminho arquivos
define('PATH_OS', __DIR__ . '/../../pdfs/');

// echo PATH_OS;

// arquivos
require_once __DIR__ . '/Database.php';

require_once __DIR__ . '/../models/Ocorrencias.php';
require_once __DIR__ . '/../models/Formularios.php';
require_once __DIR__ . '/../models/Usuarios.php';
require_once __DIR__ . '/../models/Lojas.php';
require_once __DIR__ . '/../models/Micros.php';
require_once __DIR__ . '/../models/Utils.php';
require_once __DIR__ . '/../classes/Pdf.php';

?>
