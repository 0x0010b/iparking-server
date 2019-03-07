<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/getdni', function (Request $request, Response $response) {

  $dni = $request->getParam('dni');
  
  $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.MTA5Mg.dGiplLEhICPM2JkkPSzlGKhoolfjphmv5jiIBBkh6Tw';
  $query = "
  query {
    persona(dni:\"$dni\") {
      pri_nom
      seg_nom
      ap_pat
      ap_mat
      }
  }";

  $body = json_encode($query);
  $headers = [
    'Content-Type: application/json',
    'Content-Length: '.strlen($body),
    'Authorization: Bearer ' . $token,
  ];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,"http://quertium.com/api/v1/reniec/dni");
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $jsonString = curl_exec ($ch);
  curl_close ($ch);

  $out = json_decode($jsonString);
  $res = $out->data->persona;
  
  $parking = array(
    "data"    => $res,
    "status"  => $res ? 1 : 0,
    "message" => $res ? "Servicio correcto." : "Ocurrió un error inesperado. Por favor, inténtalo más tarde."
  );

  echo json_encode($parking);

});

$app->post('/getparking', function (Request $request, Response $response) {

  $id = $request->getParam('id');
  
  $query = "SELECT * FROM parking WHERE id = :id";

  $db = new db();
  $db = $db->connect();

  $stmt = $db->prepare($query);

  $stmt->bindParam(':id', $id);
  $stmt->execute();

  $data = $stmt->fetchAll(PDO::FETCH_OBJ);
  $db = null;
    
  $response = array(
    "data"    => $data[0],
    "status"  => $data ? 1 : 0,
    "message" => $data ? "Servicio correcto." : "Ocurrió un error inesperado. Por favor, inténtalo más tarde."
  );
  
  echo json_encode($response);
});

$app->post('/setparking', function (Request $request, Response $response) {

  $id    = $request->getParam('id');
  $state = $request->getParam('state');
  
  $query = "UPDATE parking SET state = :state WHERE id = :id";

  $db = new db();
  $db = $db->connect();

  $stmt = $db->prepare($query);

  $stmt->bindParam(':id'   , $id);
  $stmt->bindParam(':state', $state);
  $stmt->execute();

  $db = null;

  $response = array(
    "data"    => [],
    "status"  => 1,
    "message" =>"Servicio correcto."
  );
  
  echo json_encode($response);
});

$app->post('/getComboParking', function (Request $request, Response $response) {

  $query = "SELECT * FROM parking WHERE state = 1";

  $db = new db();
  $db = $db->connect();

  $stmt = $db->prepare($query);

  $stmt->execute();

  $data = $stmt->fetchAll(PDO::FETCH_OBJ);
  $db = null;
    
  $response = array(
    "data"    => $data,
    "status"  => $data ? 1 : 0,
    "message" => $data ? "Servicio correcto." : "Ocurrió un error inesperado. Por favor, inténtalo más tarde."
  );
  
  echo json_encode($response);
});

