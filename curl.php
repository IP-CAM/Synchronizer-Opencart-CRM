<?php
header('Content-Type: text/html; charset=utf-8');

$json = '[
  {
    "Tiendaonline": "Mascotas Alfalfa",
    "Nombre": "Alfonso",
    "Apellidos": "Lopez Sanchez",
    "correo": "alfonsolopezsanchez23@gmail.com",
    "Telefono": "635357177",
    "Direccion": "Avenida de Sayalonga 5",
    "Ciudad": "COMPETA",
    "Estado": "COMPETA",
    "Codigo Postal": "29754",
    "Pais": "Spain",
    "forma de pago": "cod",
    "Totales": [
      {
        "Total sin impuestos": "30.6652"
      },
      {
        "Coste envio": "4.3197"
      },
      {
        "Descuento": 0
      },
      {
        "Total con impuestos": "42.3149"
      },
      {
        "IVA": "0"
      }
    ],
    "Productos": [
      {
        "codigo": "732",
        "quantity": 10,
        "opciones": []
      },
      {
        "codigo": "4767",
        "quantity": 30,
        "opciones": [
          {
            "nombre_opcion": "Tamaño",
            "valor_opcion": "Grande"
          }
        ]
      },
      {
        "codigo": "4456",
        "quantity": 1,
        "opciones": []
      },
      {
        "codigo": "1015",
        "quantity": 1,
        "opciones": []
      },
      {
        "codigo": "163",
        "quantity": 30,
        "opciones": []
      }
    ],
    "Pedido": "1153866"
  }
]';




$url = 'http://crm.mascotasalfalfa.com/sincronizador/sales_force.php';

//Initiate cURL.
$ch = curl_init($url);

//The JSON data.
//Encode the array into JSON.
$jsonDataEncoded = $json;
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//Execute the request
$result = curl_exec($ch);
curl_close($ch);
