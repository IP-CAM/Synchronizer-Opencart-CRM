<?php

require_once 'helpers/mail.php';

$json = '[
    {
      "Tiendaonline": "Pajareras",
      "Nombre": "LUIS FRANCISCO",
      "Apellidos": "CABEZA DE LA vara",
      "correo": "luisfrancis2003@hotmail.com",
      "Telefono": "609749960",
      "Direccion": "CALLE VICENTE CARBALLAL Nº15 Bajo-B",
      "Ciudad": "MADRID",
      "Estado": "MADRID",
      "Codigo Postal": "28021",
      "Pais": "Spain",
      "forma de pago": "bank_transfer",
      "Totales": [
        {
          "Total sin impuestos": "122.9739"
        },
        {
          "Coste envio": "0.0000"
        },
        {
          "Descuento": 0
        },
        {
          "Total con impuestos": "137.6588"
        },
        {
          "IVA": "0"
        }
      ],
      "Productos": [
        {
          "codigo": "7084",
          "quantity": 1,
          "opciones": [
            {
              "nombre_opcion": "Embalaje",
              "valor_opcion": "100ml"
            }
          ]
        }
      ],
      "Pedido": "11113"
    }
  ]';
  

$email = new Mail();


$email->enviarEmail($json);