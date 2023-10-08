<?php
include 'Sanitizer.php';
header('Content-Type: application/json');

function myResponse(array $response, int $status = 200)
{
    http_response_code($status);
    echo( json_encode($response) );
}

// оставляю тестовые данные, чтобы вам было удобно проверить
/**
{
    "foo": "123",
    "bar": "asd",
    "baz": "8 (707) 288-56-23",
    "one_type_arr": [
        "1",
        "2",
        "3",
        "4"
    ],
    "it_is_struct": {
        "name": "Toyota",
        "model": "Camry",
        "color": "White",
        "born": "2017"
    },
    "for_add": "no"
}
*/

// принимаем http запрос
$request = file_get_contents("php://input");
if ($request === false) {
    myResponse([
        'success' => false,
        'message' => 'Failed while reading http body'
    ], 422);
    return;
}

// парсим json
$data = json_decode($request, true);
if ($data === null) {
    myResponse([
        'success' => false,
        'message' => 'Failed while parsing json'
    ], 422);
    return;
}

$rules = [
    'foo'          => [ 'integer' ],
    'bar'          => [ 'string' ],
    'baz'          => [ 'phone_number_kz' ],
    'one_type_arr' => [ 'one_type_array' ],
    'it_is_struct'  => [ 'structure', [ 'name', 'model', 'color', 'born' ] ],
    'for_add'      => [
        'my_bool',
        function ($value) {
            if ($value === 'yes' || $value === 'no') {
                return true;
            }

            return false;
        }
    ]
];

try {
    $result = ( new Sanitizer($rules) )->sanitize($data);
} catch (Exception $e) {
    myResponse([
        'success' => false,
        'message' => $e->getMessage() . ' ' . $e->getFile() . ' line: ' . $e->getLine(),
    ], 500);
    return;
}

if (count($result[ 'errors' ]) === 0) {
    myResponse([
        'success' => true,
        'data'    => $result[ 'data' ],
    ]);
} else {
    myResponse([
        'success' => false,
        'errors'  => $result[ 'errors' ],
    ]);
}
return;

