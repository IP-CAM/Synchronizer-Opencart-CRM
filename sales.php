<?php

require_once 'config/config.php';
require_once 'controller/clienteif.php';
require_once 'controller/clienteqbn.php';
require_once 'controller/productoif.php';
require_once 'controller/sales.php';
require_once 'controller/variantsif.php';
require_once 'log.php';
require_once 'helpers/mail.php';


//instanciamos las clases
$proif = new ProductControllerIF();
$clieif = new ClienteControllerIF();
$sale = new SalesControllerIF();
$variants = new VariantsIFControllerIF();
$log = new Log();

//consumir cURL json
$data = json_decode(file_get_contents('php://input'), true);

try {

    $referencia = $sale->obtenerReferencia($data[0]['Pedido']);

    //---------------AQUI GENERO AL CREADOR DEL PEDIDO SEGUN LA TIENDA QUE VIENE EN EL JSON--------------------

    if ($referencia->num_rows > 0) {
    } else {

        if ($data[0]['Tiendaonline'] == 'Mascotas Alfalfa') {

            $created_by = 4;
        }

        if ($data[0]['Tiendaonline'] == 'Mascotas Alfalfa Movil') {

            $created_by = 6;
        }

        if ($data[0]['Tiendaonline'] == 'Pajareras') {

            $created_by = 5;
        }

        if ($data[0]['Tiendaonline'] == 'Pajareras Movil') {

            $created_by = 7;
        }

        //---------------AQUI VERIFICAMOS SI EL CLIENTE EXISTE--------------------

        //verficamos el cliente por el email
        $clieid = $clieif->getCliente($data[0]['correo']);

        //array para ivas y cantidades
        $arrc = array();
        $idpago = false;
        $pricevar = 0;

        //SI EXISTE CLIENTE
        if ($clieid->num_rows > 0) {



            //---------------AQUI CREAMOS LA VENTA --------------------
            //creamos el objeto para la venta 

            $sma_sales = new stdClass();
            $sma_sales->reference_no = $data[0]['Pedido'];
            $sma_sales->customer_id =  $clieid->fetch_object()->id;
            $sma_sales->customer = utf8_decode($data[0]['Nombre'] . ' ' . $data[0]['Apellidos']);
            $sma_sales->biller_id = valor_tres;
            $sma_sales->biller = vendedor;
            $sma_sales->warehouse_id = valor_dos;
            $sma_sales->note = string_vacio;
            $sma_sales->staff_note = string_vacio;
            $sma_sales->total = $data[0]['Totales'][0]['Total sin impuestos'];
            $sma_sales->product_discount =  valor_cero;
            $sma_sales->order_discount_id = str_replace('-', '', $data[0]['Totales'][2]['Descuento']);
            $sma_sales->total_discount = str_replace('-', '', $data[0]['Totales'][2]['Descuento']);
            $sma_sales->order_discount = str_replace('-', '', $data[0]['Totales'][2]['Descuento']);


            //--------------RECORREMOS LOS PRODUCTOS DE LA VENTA---------------------
            //recorremos sus productos
            foreach ($data[0]['Productos'] as $value) {

                //verificamos por su codigo
                $producto = $proif->getExistProductsCode($value['codigo']);

                //---------------AQUI HAGO LA BUSQUEDA POR CODIGO-VALOR DE OPCION--------------------
                //para poder validar si las opciones existen
                if ($producto->num_rows == 0) {
                    if (count($value['opciones']) > 0) {
                        foreach ($value['opciones'] as $option) {

                            $name_vari = $value['codigo'] . '-' . $option['valor_opcion'];

                            $pro_combo_var = $proif->getExistProductsCode($name_vari);

                            if ($pro_combo_var->num_rows > 0) {

                                while ($pro_combo = $pro_combo_var->fetch_object()) {

                                    $item_combo = $proif->getItemsCombo($pro_combo->id);

                                    while ($items = $item_combo->fetch_object()) {
                                    }
                                }
                            }
                        }
                    }
                } else {

                    //obtenemos el iva total de cada producto   IVA(2DECIMALES REDONDEADO) * CANTIDAD
                    //obtenemos su cantidad y almacenamos en un array
                    while ($productos = $producto->fetch_object()) {

                        //recorremos sus obciones para validar si existen, si no, no se podra registrar la venta
                        if (count($value['opciones']) > 0) {
                            foreach ($value['opciones'] as $option) {
                                $name_vari = $option['nombre_opcion'] . ' ' . $option['valor_opcion'];
                            }

                            $pro_variant = $proif->getProVarPrice(utf8_decode($name_vari), $productos->id);

                            if ($pro_variant->num_rows > 0) {
                            } else {


                                $log->logs('Error por opciones: ' .  json_encode($data));

                                $email = new Mail();
                                $email->enviarEmail(json_encode($data));
                                exit;
                            }
                        }

                        if ($productos->tax_rate == 2) {

                            array_push($arrc, [
                                "tax_rate" => (round(($productos->price * 0.10), 2, PHP_ROUND_HALF_UP) * $value['quantity']),
                                "quantity" => $value['quantity']
                            ]);
                        }
                        if ($productos->tax_rate == 3) {

                            array_push($arrc, [
                                "tax_rate" => (round(($productos->price * 0.04), 2, PHP_ROUND_HALF_UP) * $value['quantity']),
                                "quantity" => $value['quantity']
                            ]);
                        }
                        if ($productos->tax_rate == 4) {

                            array_push($arrc, [
                                "tax_rate" => (round(($productos->price * 0.21), 2, PHP_ROUND_HALF_UP) * $value['quantity']),
                                "quantity" => $value['quantity']
                            ]);
                        }
                    }
                }
            }

            //recorremos el array y sumamos los ivas del crm para obtener el iva total
            $total_tax = 0;
            $total_quantity = 0;
            for ($i = 0; $i < count($arrc); $i++) {

                $total_tax = $total_tax + $arrc[$i]['tax_rate'];
                $total_quantity = $total_quantity + $arrc[$i]['quantity'];
            }

            //seguimos llenando el objeto de venta
            $sma_sales->product_tax = $total_tax;
            $sma_sales->order_tax_id = valor_uno;
            $sma_sales->order_tax = valor_cero;
            $sma_sales->total_tax = $total_tax;
            $sma_sales->shipping = $data[0]['Totales'][1]['Coste envio'];
            $sma_sales->grand_total = $data[0]['Totales'][3]['Total con impuestos'];

            //segun la forma de pago registramos el estado del pago
            if ($data[0]['forma de pago'] == 'redsys' || $data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'ptm') {
                $sma_sales->payment_status = completed;
                $sma_sales->sale_status = pending;
            } else {
                $sma_sales->payment_status = pending;
                $sma_sales->sale_status = pending;
            }
            $sma_sales->payment_term = valor_cero;
            $sma_sales->due_date = var_null;
            $sma_sales->created_by = $created_by;
            $sma_sales->updated_by = var_null;;
            $sma_sales->updated_at = var_null;;
            $sma_sales->total_items = $total_quantity;
            $sma_sales->pos = valor_cero;
            $sma_sales->paid = valor_cero;
            $sma_sales->return_id = var_null;
            $sma_sales->surcharge = valor_cero;
            $sma_sales->attachment = var_null;
            $sma_sales->return_sale_ref = var_null;
            $sma_sales->sale_id = var_null;
            $sma_sales->return_sale_total = valor_cero;
            $sma_sales->rounding = var_null;
            $sma_sales->suspend_note = var_null;
            $sma_sales->api = valor_cero;
            $sma_sales->shop = valor_cero;
            $sma_sales->address_id = var_null;
            $sma_sales->reserve_id = var_null;
            //el hash conforme al crm
            $sma_sales->hash = hash('sha256', microtime() . mt_rand());
            $sma_sales->manual_payment = var_null;
            $sma_sales->cgst = var_null;
            $sma_sales->sgst = var_null;
            $sma_sales->igst = var_null;

            //validamos el metodo de pago
            if ($data[0]['forma de pago'] == 'redsys') {
                $sma_sales->payment_method = 'CC';
            }
            if ($data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'ptm'  || $data[0]['forma de pago'] == 'cod') {
                $sma_sales->payment_method = 'otros';
            }

            if ($data[0]['forma de pago'] == 'bank_transfer') {
                $sma_sales->payment_method = 'deposit';
            }

            //insertamos la venta
            $idsales = $sale->InsertSales($sma_sales);

            //obtenemos su id y si se registro bien pasamos a registrar sus items
            if ($idsales) {

                //recorremos los productos
                foreach ($data[0]['Productos'] as $value) {

                    //validamos que existan
                    $producto = $proif->getExistProductsCode($value['codigo']);
                    
                    if ($producto->num_rows == 0) {
                        //---------------AQUI HAGO LA BUSQUEDA POR CODIGO-VALOR DE OPCION--------------------
                        if (count($value['opciones']) > 0) {

                            //recorro las opciones
                            foreach ($value['opciones'] as $option) {

                                //concateno el codigo que se buscara
                                $name_vari = $value['codigo'] . '-' . $option['valor_opcion'];
                                //hago la busqueda
                                $pro_combo_var = $proif->getExistProductsCode($name_vari);

                                if ($pro_combo_var->num_rows > 0) {

                                    while ($pro_combo = $pro_combo_var->fetch_object()) {

                                        $option_id = '';
                                        $pricevar = 0;
                                        //creamos objeto de items
                                        $sma_sales_items = new stdClass();
                                        $sma_sales_items->sale_id = $idsales;
                                        $sma_sales_items->product_id = $pro_combo->id;
                                        $sma_sales_items->product_code = $pro_combo->code;
                                        $sma_sales_items->product_name = $pro_combo->name;
                                        $sma_sales_items->product_type = 'combo';

                                        $sma_sales_items->option_id = var_null;
                                        $price_unit = $pro_combo->price;

                                        $sma_sales_items->net_unit_price =  round($price_unit, 2, PHP_ROUND_HALF_UP);
                                        //validamos que iva va registrado y calculamos
                                        if ($pro_combo->tax_rate == 2) {
                                            $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.10), 2, PHP_ROUND_HALF_UP);
                                            $sma_sales_items->item_tax =  round(($price_unit * 0.10), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                            $sma_sales_items->tax_rate_id =  $pro_combo->tax_rate;
                                            $sma_sales_items->tax =  diez_porciento;
                                        }

                                        if ($pro_combo->tax_rate == 3) {
                                            $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.04), 2, PHP_ROUND_HALF_UP);
                                            $sma_sales_items->item_tax =    round(($price_unit * 0.04), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                            $sma_sales_items->tax_rate_id =  $pro_combo->tax_rate;
                                            $sma_sales_items->tax =  cuatro_porciento;
                                        }

                                        if ($pro_combo->tax_rate == 4) {
                                            $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.21), 2, PHP_ROUND_HALF_UP);
                                            $sma_sales_items->item_tax =   round(($price_unit * 0.21), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                            $sma_sales_items->tax_rate_id =  $pro_combo->tax_rate;
                                            $sma_sales_items->tax =  veintiuno_porciento;
                                        }

                                        $sma_sales_items->discount =  valor_cero;
                                        $sma_sales_items->item_discount =  valor_cero;
                                        $sma_sales_items->sub_total =  $sma_sales_items->unit_price * $value['quantity'];

                                        $sma_sales_items->quantity = $value['quantity'];
                                        $sma_sales_items->warehouse = tienda_dos;
                                        $sma_sales_items->serial_no = string_vacio;
                                        $sma_sales_items->real_unit_price = round($pro_combo->price, 2, PHP_ROUND_HALF_UP);
                                        $sma_sales_items->sale_item_id = var_null;
                                        $sma_sales_items->product_unit_id = valor_uno;
                                        $sma_sales_items->product_unit_code = product_unit_code;
                                        $sma_sales_items->unity_quantity = $value['quantity'];
                                        $sma_sales_items->comment = var_null;
                                        $sma_sales_items->gst = var_null;
                                        $sma_sales_items->cgst = var_null;
                                        $sma_sales_items->sgst = var_null;
                                        $sma_sales_items->igst = var_null;

                                        $id_arr = array();
                                        //Insertamos los items de la venta (sus productos)
                                        $iditem = $sale->InsertItems($sma_sales_items);

                                        array_push($id_arr,$iditem);
                                     if ($sma_sales->sale_status == completed) {
                                        $product_combo = $proif->getCombo($name_vari);

                                        if ($product_combo->num_rows > 0) {
                                            while ($producto_c = $product_combo->fetch_object()) {
                                                $item_combo = $proif->getItemsCombo($producto_c->id);

                                                while ($item = $item_combo->fetch_object()) {
                                                    $procut_i = $proif->getExistProductsCode($item->item_code);
                                                    //creamos el objeto para la tabla de costing
                                                    while($procut_is = $procut_i->fetch_object()){
                                                        $sma_costing = new stdClass();
                                                        $sma_costing->product_id = $procut_is->id;
                                                        $sma_costing->sale_item_id = $id_arr[0];
                                                        $sma_costing->sale_id = $idsales;
                                                        //necesitamos los datos de purchase de cada producto para relacionar con la tabla purchase
                                                        $idpur = $sale->getPurchase($procut_is->id);
    
                                                        if ($idpur->num_rows > 0) {
                                                            while ($purchase = $idpur->fetch_object()) {
                                                                $sma_costing->purchase_item_id = $purchase->id;
                                                                $sma_costing->purchase_net_unit_cost = $purchase->net_unit_cost;
                                                                $sma_costing->purchase_unit_cost = $purchase->unit_cost;
                                                                $sma_costing->sale_net_unit_price =  round($procut_is->price, 2, PHP_ROUND_HALF_UP);;
                                                            }
                                                        } else {
                                                            $sma_costing->purchase_item_id = 0;
                                                            $sma_costing->purchase_net_unit_cost = 0;
                                                            $sma_costing->purchase_unit_cost = 0;
                                                            $sma_costing->sale_net_unit_price =  0;
                                                        }
    
                                                        $sma_costing->quantity = $value['quantity'];

                                                        if ($procut_is->tax_rate == 2) {
                                                            $sma_costing->sale_unit_price =  round($procut_is->price + ($procut_is->price * 0.10), 2, PHP_ROUND_HALF_UP);

                                                        }
                
                                                        if ($procut_is->tax_rate == 3) {
                                                            $sma_costing->sale_unit_price = round($procut_is->price + ($procut_is->price * 0.04), 2, PHP_ROUND_HALF_UP);

                                                        }
                
                                                        if ($procut_is->tax_rate == 4) {
                                                            $sma_costing->sale_unit_price =round($procut_is->price + ($procut_is->price * 0.21), 2, PHP_ROUND_HALF_UP);
                                                        }

                                                        
                                                        //descuento de balance segun el crm
                                                        $sma_costing->quantity_balance = "-".($item->quantity * $value['quantity']) ;
                                                        $sma_costing->inventory = valor_uno;
                                                        $sma_costing->overselling = var_null;
                                                        $sma_costing->option_id =  var_null;
    
                                                        //insertamos el costing
                                                        $iditem = $sale->InsertCosting($sma_costing);
                                                    }
                                                   
                                                }
                                            }
                                        }

                                        //segun estas formas de pago se hace el descuento en las tablas de stock
                                        if ($data[0]['forma de pago'] == 'redsys' || $data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'ptm') {


                                            if ($sma_sales->sale_status == completed) {

                                                if ($pro_combo->type == 'combo') {

                                                        $product_combo = $proif->getCombo($name_vari);


                                                        while ($producto_c = $product_combo->fetch_object()) {

                                                            $item_combo = $proif->getItemsCombo($producto_c->id);

                                                            while ($item = $item_combo->fetch_object()) {


                                                                $procut_i = $proif->getExistProductsCode($item->item_code);

                                                                while ($items_combo = $procut_i->fetch_object()) {
                                                                    $restar = new stdClass();
                                                                    $restar->product_id = $items_combo->id;
                                                                    $restar->quantity = $item->quantity * $value['quantity'];
                                                                    $restar->option_id =  var_null;

                                                                    $result = $sale->restarProduct($restar);
                                                                    $results = $sale->restarProductWarehouse($restar);
                                                                    $resultss = $sale->restarPurchaseItems($restar);
                                                                }
                                                            }
                                                        }
                                                    
                                                }
                                            }
                                        }
                                     }
                                    }
                                }
                            }

                        }
                        //---------------AQUI TERMINA : EL PRODUCTO COMBO CON VARIANTES--------------------
                    } else {

                        //si existen
                        while ($productos = $producto->fetch_object()) {
                            $option_id = '';
                            $pricevar = 0;
                            //creamos objeto de items
                            $sma_sales_items = new stdClass();
                            $sma_sales_items->sale_id = $idsales;
                            $sma_sales_items->product_id = $productos->id;
                            $sma_sales_items->product_code = $productos->code;
                            $sma_sales_items->product_name = $productos->name;

                            if ($productos->type == 'combo') {
                                $sma_sales_items->product_type = 'combo';
                            } else {
                                $sma_sales_items->product_type = standard;
                            }

                            if (count($value['opciones']) > 0) {
                                foreach ($value['opciones'] as $option) {
                                    $name_vari = $option['nombre_opcion'] . ' ' . $option['valor_opcion'];
                                }

                                $pro_variant = $proif->getProVarPrice(utf8_decode($name_vari), $productos->id);
                                if ($pro_variant->num_rows > 0) {
                                    while ($prodts = $pro_variant->fetch_object()) {
                                        $pricevar =  $prodts->price;
                                        $option_id = $prodts->id;
                                    }
                                }
                            }
                            

                            if (empty($option_id)) {
                                $sma_sales_items->option_id = var_null;
                            } else {
                                if ($productos->type == 'combo') {
                                    $sma_sales_items->option_id = var_null;
                                } else {
                                    $sma_sales_items->option_id = $option_id;
                                }
                            }
                            $price_unit = $productos->price + $pricevar;
                            $sma_sales_items->net_unit_price =  round($price_unit, 2, PHP_ROUND_HALF_UP);
                            //validamos que iva va registrado y calculamos 
                            if ($productos->tax_rate == 2) {
                                $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.10), 2, PHP_ROUND_HALF_UP);
                                $sma_sales_items->item_tax =  round(($price_unit * 0.10), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                $sma_sales_items->tax_rate_id =  $productos->tax_rate;
                                $sma_sales_items->tax =  diez_porciento;
                            }

                            if ($productos->tax_rate == 3) {
                                $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.04), 2, PHP_ROUND_HALF_UP);
                                $sma_sales_items->item_tax =    round(($price_unit * 0.04), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                $sma_sales_items->tax_rate_id =  $productos->tax_rate;
                                $sma_sales_items->tax =  cuatro_porciento;
                            }

                            if ($productos->tax_rate == 4) {
                                $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.21), 2, PHP_ROUND_HALF_UP);
                                $sma_sales_items->item_tax =   round(($price_unit * 0.21), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                $sma_sales_items->tax_rate_id =  $productos->tax_rate;
                                $sma_sales_items->tax =  veintiuno_porciento;
                            }

                            $sma_sales_items->discount =  valor_cero;
                            $sma_sales_items->item_discount =  valor_cero;
                            if ($pricevar == 0) {
                                if ($productos->type == 'combo') {
                                    $sma_sales_items->sub_total =  $sma_sales_items->unit_price * $value['quantity'];
                                } else {
                                    $sma_sales_items->sub_total =  $sma_sales_items->unit_price * $value['quantity'];
                                }
                            } else {
                                $sma_sales_items->sub_total =  $sma_sales_items->unit_price * $value['quantity'];
                            }
                            $sma_sales_items->quantity = $value['quantity'];
                            $sma_sales_items->warehouse = tienda_dos;
                            $sma_sales_items->serial_no = string_vacio;
                            $sma_sales_items->real_unit_price = round($productos->price, 2, PHP_ROUND_HALF_UP);
                            $sma_sales_items->sale_item_id = var_null;
                            $sma_sales_items->product_unit_id = valor_uno;
                            $sma_sales_items->product_unit_code = product_unit_code;
                            $sma_sales_items->unity_quantity = $value['quantity'];
                            $sma_sales_items->comment = var_null;
                            $sma_sales_items->gst = var_null;
                            $sma_sales_items->cgst = var_null;
                            $sma_sales_items->sgst = var_null;
                            $sma_sales_items->igst = var_null;

                            $id_arr = array();
                            //Insertamos los items de la venta (sus productos)
                             $iditem = $sale->InsertItems($sma_sales_items);
                             array_push($id_arr,$iditem);

                            if ($productos->type == 'combo') {

                                if ($sma_sales->sale_status == completed) {
                                    $product_combo = $proif->getCombo($value['codigo']);

                                    if ($product_combo->num_rows > 0) {
                                        while ($producto_c = $product_combo->fetch_object()) {
                                            $item_combo = $proif->getItemsCombo($producto_c->id);

                                            while ($item = $item_combo->fetch_object()) {
                                                $procut_i = $proif->getExistProductsCode($item->item_code);
                                                //creamos el objeto para la tabla de costing
                                                while ($procut_is = $procut_i->fetch_object()) {
                                                    $sma_costing = new stdClass();
                                                    $sma_costing->product_id = $procut_is->id;
                                                    $sma_costing->sale_item_id = $id_arr[0];
                                                    $sma_costing->sale_id = $idsales;
                                                    //necesitamos los datos de purchase de cada producto para relacionar con la tabla purchase
                                                    $idpur = $sale->getPurchase($procut_is->id);

                                                    if ($idpur->num_rows > 0) {
                                                        while ($purchase = $idpur->fetch_object()) {
                                                            $sma_costing->purchase_item_id = $purchase->id;
                                                            $sma_costing->purchase_net_unit_cost = $purchase->net_unit_cost;
                                                            $sma_costing->purchase_unit_cost = $purchase->unit_cost;
                                                            $sma_costing->sale_net_unit_price =  round($procut_is->price, 2, PHP_ROUND_HALF_UP);
                                                            ;
                                                        }
                                                    } else {
                                                        $sma_costing->purchase_item_id = 0;
                                                        $sma_costing->purchase_net_unit_cost = 0;
                                                        $sma_costing->purchase_unit_cost = 0;
                                                        $sma_costing->sale_net_unit_price =  0;
                                                    }

                                                    $sma_costing->quantity = $value['quantity'];

                                                    if ($procut_is->tax_rate == 2) {
                                                        $sma_costing->sale_unit_price =  round($procut_is->price + ($procut_is->price * 0.10), 2, PHP_ROUND_HALF_UP);
                                                    }
        
                                                    if ($procut_is->tax_rate == 3) {
                                                        $sma_costing->sale_unit_price = round($procut_is->price + ($procut_is->price * 0.04), 2, PHP_ROUND_HALF_UP);
                                                    }
        
                                                    if ($procut_is->tax_rate == 4) {
                                                        $sma_costing->sale_unit_price =round($procut_is->price + ($procut_is->price * 0.21), 2, PHP_ROUND_HALF_UP);
                                                    }

                                                
                                                    //descuento de balance segun el crm
                                                    $sma_costing->quantity_balance = "-".($item->quantity * $value['quantity']) ;
                                                    $sma_costing->inventory = valor_uno;
                                                    $sma_costing->overselling = var_null;
                                                    $sma_costing->option_id =  var_null;

                                                    //insertamos el costing
                                                    $iditem = $sale->InsertCosting($sma_costing);
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                //creamos el objeto para la tabla de costing
                                $sma_costing = new stdClass();
                                $sma_costing->product_id = $productos->id;
                                $sma_costing->sale_item_id = $iditem;
                                $sma_costing->sale_id = $idsales;
                                //necesitamos los datos de purchase de cada producto para relacionar con la tabla purchase
                                $idpur = $sale->getPurchase($productos->id);

                                if ($idpur->num_rows > 0) {
                                    while ($purchase = $idpur->fetch_object()) {
                                        $sma_costing->purchase_item_id = $purchase->id;
                                        $sma_costing->purchase_net_unit_cost = $purchase->net_unit_cost;
                                        $sma_costing->purchase_unit_cost = $purchase->unit_cost;
                                        $sma_costing->sale_net_unit_price =  $purchase->unit_cost;
                                    }
                                } else {

                                    $sma_costing->purchase_item_id = 0;
                                    $sma_costing->purchase_net_unit_cost = 0;
                                    $sma_costing->purchase_unit_cost = 0;
                                    $sma_costing->sale_net_unit_price =  0;
                                }


                                $sma_costing->quantity = $value['quantity'];

                                $sma_costing->sale_unit_price = $productos->price;
                                //descuento de balance segun el crm
                                $sma_costing->quantity_balance = $productos->quantity - $value['quantity'];
                                $sma_costing->inventory = valor_uno;
                                $sma_costing->overselling = var_null;
                                if (empty($option_id)) {
                                    $sma_costing->option_id =  var_null;
                                } else {
                                    $sma_costing->option_id =  $option_id;
                                }
                                if ($sma_sales->sale_status == completed) {
                                    //insertamos el costing
                                    $iditem = $sale->InsertCosting($sma_costing);
                                }
                            }
                            //segun estas formas de pago se hace el descuento en las tablas de stock
                            if ($data[0]['forma de pago'] == 'redsys' || $data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'ptm') {


                                if ($sma_sales->sale_status == completed) {

                                    if ($productos->type == 'combo') {

                                        $product_combo = $proif->getCombo($value['codigo']);

                                        if ($product_combo->num_rows > 0) {
                                            while ($producto_c = $product_combo->fetch_object()) {

                                                $item_combo = $proif->getItemsCombo($producto_c->id);

                                                while ($item = $item_combo->fetch_object()) {
                                                    $procut_i = $proif->getExistProductsCode($item->item_code);

                                                    while ($items_combo = $procut_i->fetch_object()) {
                                                        $restar = new stdClass();
                                                        $restar->product_id = $items_combo->id;
                                                        $restar->quantity = $item->quantity * $value['quantity'];
                                                        $restar->option_id =  var_null;

                                                        $result = $sale->restarProduct($restar);
                                                        $results = $sale->restarProductWarehouse($restar);
                                                        $resultss = $sale->restarPurchaseItems($restar);
                                                    }
                                                }
                                            }
                                        } else {

                                            $product_combo = $proif->getCombo($name_vari);


                                            while ($producto_c = $product_combo->fetch_object()) {

                                                $item_combo = $proif->getItemsCombo($producto_c->id);

                                                while ($item = $item_combo->fetch_object()) {


                                                    $procut_i = $proif->getExistProductsCode($item->item_code);

                                                    while ($items_combo = $procut_i->fetch_object()) {
                                                        $restar = new stdClass();
                                                        $restar->product_id = $items_combo->id;
                                                        $restar->quantity = $item->quantity * $value['quantity'];
                                                        $restar->option_id =  var_null;

                                                        $result = $sale->restarProduct($restar);
                                                        $results = $sale->restarProductWarehouse($restar);
                                                        $resultss = $sale->restarPurchaseItems($restar);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $restar = new stdClass();
                                        $restar->product_id = $productos->id;
                                        $restar->quantity = $value['quantity'];
                                        $restar->option_id =  $option_id;
                                        $result = $sale->restarProduct($restar);
                                        $results = $sale->restarProductWarehouse($restar);
                                        $resultss = $sale->restarPurchaseItems($restar);

                                        if (count($value['opciones']) > 0) {
                                            foreach ($value['opciones'] as $option) {
                                                $restar->variants = $option['nombre_opcion'] . ' ' . $option['valor_opcion'];
                                            }


                                            $variantp = $sale->restarVariantsProduct($restar);
                                            $restwv = $sale->restarWareHouseProductVariants($restar);
                                            //var_dump("restwv:" . $restwv);



                                        }
                                    }
                                }
                                //var_dump("result: " . $result);
                                //var_dump("results: " . $results);
                                //var_dump("resultss: " . $resultss);
                            }
                        }
                    }
                }

                //para registrar los pagos validamos los metodos de pago
                if ($data[0]['forma de pago'] == 'redsys' || $data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'pmt') {
                    $pago = new stdClass();
                    $pago->sale_id = $idsales;
                    //segun estas formas el pago es el total de la venta                                
                    $pago->amount = $data[0]['Totales'][3]['Total con impuestos'];
                    $pago->created_by = $created_by;
                    $pago->type = 'received';

                    //validamos que metodo en la tienda y le damos su equivalencia segun el crm
                    if ($data[0]['forma de pago'] == 'redsys') {
                        $pago->paid_by = 'CC';
                    }

                    if ($data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'pmt') {
                        $pago->paid_by = 'otros';
                    }

                    //insertamos pago
                    $idpago = $sale->InsertPago($pago);
                }

                if ($idpago) {
                    $status = pagado;
                    $paid = $data[0]['Totales'][3]['Total con impuestos'];
                    $result =  $sale->UpdateVentaPago($status, $pago->sale_id, $paid);
                }
            } else {
                throw new Exception('No se registro la venta ' . "\n" .  json_encode($data) . "\n" . "-------------" . "\n");
            }
        } else {

            //si no existe el cliente lo creamos
            $client = new stdClass();
            $client->group_id = valor_tres;
            $client->group_name = group_name;
            $client->customer_group_id = valor_uno;
            $client->customer_group_name = customer_group_name;
            $client->name = utf8_decode($data[0]['Nombre'] . ' ' . $data[0]['Apellidos']);
            $client->company = utf8_decode($data[0]['Nombre'] . ' ' . $data[0]['Apellidos']);
            $client->vat_no = string_vacio;
            $client->address = utf8_decode($data[0]['Direccion']);
            $client->city = utf8_decode($data[0]['Ciudad']);
            $client->state = utf8_decode($data[0]['Estado']);
            $client->postal_code = $data[0]['Codigo Postal'];
            $client->country = utf8_decode($data[0]['Pais']);
            $client->phone = $data[0]['Telefono'];
            $client->email = $data[0]['correo'];
            $client->cf1 = valor_guion;
            $client->cf2 = valor_guion;
            $client->cf3 = valor_guion;
            $client->cf4 = valor_guion;
            $client->cf5 = valor_guion;
            $client->cf6 = valor_guion;
            $client->invoice_footer = var_null;
            $client->payment_term = valor_cero;
            $client->logo = 'logo.png';
            $client->award_points = valor_cero;
            $client->deposit_amount = var_null;
            $client->price_group_id = valor_uno;
            $client->price_group_name = customer_group_name;
            $client->gst_no = string_vacio;

            $idcl = $clieif->Insert($client);

            //una vez registrado obtenemos su id cliente 
            if ($idcl) {

                //creamos el objeto para la venta 
                $sma_sales = new stdClass();
                $sma_sales->reference_no = $data[0]['Pedido'];
                $sma_sales->customer_id =  $idcl;
                $sma_sales->customer = utf8_decode($data[0]['Nombre'] . ' ' . $data[0]['Apellidos']);
                $sma_sales->biller_id = valor_tres;
                $sma_sales->biller = vendedor;
                $sma_sales->warehouse_id = valor_dos;
                $sma_sales->note = string_vacio;
                $sma_sales->staff_note = string_vacio;
                $sma_sales->total = $data[0]['Totales'][0]['Total sin impuestos'];
                $sma_sales->product_discount =  valor_cero;
                $sma_sales->order_discount_id = str_replace('-', '', $data[0]['Totales'][2]['Descuento']);
                $sma_sales->total_discount = str_replace('-', '', $data[0]['Totales'][2]['Descuento']);
                $sma_sales->order_discount = str_replace('-', '', $data[0]['Totales'][2]['Descuento']);


                //recorremos sus productos
                foreach ($data[0]['Productos'] as $value) {

                    //verificamos por su codigo
                    $producto = $proif->getExistProductsCode($value['codigo']);
                    if ($producto->num_rows == 0) {
                        //echo "no hay producto codigo : " . $pro['codigo'];
                    } else {


                        //obtenemos el iva total de cada producto   IVA(2DECIMALES REDONDEADO) * CANTIDAD
                        //obtenemos su cantidad y almacenamos en un array
                        while ($productos = $producto->fetch_object()) {

                            //recorremos sus obciones para validar si existen, si no, no se podra registrar la venta
                            if (count($value['opciones']) > 0) {
                                foreach ($value['opciones'] as $option) {
                                    $name_vari = $option['nombre_opcion'] . ' ' . $option['valor_opcion'];
                                }

                                $pro_variant = $proif->getProVarPrice(utf8_decode($name_vari), $productos->id);

                                if ($pro_variant->num_rows > 0) {
                                } else {
                                    $log->logs('Error por opciones: ' .  json_encode($data));

                                    $email = new Mail();
                                    $email->enviarEmail(json_encode($data));
                                    exit;
                                }
                            }

                            if ($productos->tax_rate == 2) {

                                array_push($arrc, [
                                    "tax_rate" => (round(($productos->price * 0.10), 2, PHP_ROUND_HALF_UP) * $value['quantity']),
                                    "quantity" => $value['quantity']
                                ]);
                            }

                            if ($productos->tax_rate == 3) {

                                array_push($arrc, [
                                    "tax_rate" => (round(($productos->price * 0.04), 2, PHP_ROUND_HALF_UP) * $value['quantity']),
                                    "quantity" => $value['quantity']
                                ]);
                            }
                            if ($productos->tax_rate == 4) {

                                array_push($arrc, [
                                    "tax_rate" => (round(($productos->price * 0.21), 2, PHP_ROUND_HALF_UP) * $value['quantity']),
                                    "quantity" => $value['quantity']
                                ]);
                            }
                        }
                    }
                }

                //recorremos el array y sumamos los ivas del crm para obtener el iva total
                $total_tax = 0;
                $total_quantity = 0;
                for ($i = 0; $i < count($arrc); $i++) {

                    $total_tax = $total_tax + $arrc[$i]['tax_rate'];
                    $total_quantity = $total_quantity + $arrc[$i]['quantity'];
                }

                //seguimos llenando el objeto de venta
                $sma_sales->product_tax = $total_tax;
                $sma_sales->order_tax_id = valor_uno;
                $sma_sales->order_tax = valor_cero;
                $sma_sales->total_tax = $total_tax;
                $sma_sales->shipping = $data[0]['Totales'][1]['Coste envio'];
                $sma_sales->grand_total = $data[0]['Totales'][3]['Total con impuestos'];


                //segun la forma de pago registramos el estado del pago
                if ($data[0]['forma de pago'] == 'redsys' || $data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'ptm') {
                    $sma_sales->payment_status = completed;
                    $sma_sales->sale_status = pending;
                } else {
                    $sma_sales->payment_status = pending;
                    $sma_sales->sale_status = pending;
                }
                $sma_sales->payment_term = valor_cero;
                $sma_sales->due_date = var_null;
                $sma_sales->created_by = $created_by;
                $sma_sales->updated_by = var_null;
                $sma_sales->updated_at = var_null;
                $sma_sales->total_items = $total_quantity;
                $sma_sales->pos = valor_cero;
                $sma_sales->paid = valor_cero;
                $sma_sales->return_id = var_null;
                $sma_sales->surcharge = valor_cero;
                $sma_sales->attachment = var_null;
                $sma_sales->return_sale_ref = var_null;
                $sma_sales->sale_id = var_null;
                $sma_sales->return_sale_total = valor_cero;
                $sma_sales->rounding = var_null;
                $sma_sales->suspend_note = var_null;
                $sma_sales->api = valor_cero;
                $sma_sales->shop = valor_cero;
                $sma_sales->address_id = var_null;
                $sma_sales->reserve_id = var_null;
                //el hash conforme al crm
                $sma_sales->hash = hash('sha256', microtime() . mt_rand());
                $sma_sales->manual_payment = var_null;
                $sma_sales->cgst = var_null;
                $sma_sales->sgst = var_null;
                $sma_sales->igst = var_null;

                //validamos el metodo de pago
                if ($data[0]['forma de pago'] == 'redsys') {
                    $sma_sales->payment_method = 'CC';
                }
                if ($data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'ptm'  || $data[0]['forma de pago'] == 'cod') {
                    $sma_sales->payment_method = 'otros';
                }

                if ($data[0]['forma de pago'] == 'bank_transfer') {
                    $sma_sales->payment_method = 'deposit';
                }

                //insertamos la venta
                $idsales = $sale->InsertSales($sma_sales);

                //obtenemos su id y si se registro bien pasamos a registrar sus items
                if ($idsales) {

                    //recorremos los productos
                    foreach ($data[0]['Productos'] as $value) {


                        //validamos que existan
                        $producto = $proif->getExistProductsCode($value['codigo']);
                        if ($producto->num_rows == 0) {
                            //---------------AQUI HAGO LA BUSQUEDA POR CODIGO-VALOR DE OPCION--------------------
                        if (count($value['opciones']) > 0) {

                            //recorro las opciones
                            foreach ($value['opciones'] as $option) {

                                //concateno el codigo que se buscara
                                $name_vari = $value['codigo'] . '-' . $option['valor_opcion'];
                                //hago la busqueda
                                $pro_combo_var = $proif->getExistProductsCode($name_vari);

                                if ($pro_combo_var->num_rows > 0) {

                                    while ($pro_combo = $pro_combo_var->fetch_object()) {

                                        $option_id = '';
                                        $pricevar = 0;
                                        //creamos objeto de items
                                        $sma_sales_items = new stdClass();
                                        $sma_sales_items->sale_id = $idsales;
                                        $sma_sales_items->product_id = $pro_combo->id;
                                        $sma_sales_items->product_code = $pro_combo->code;
                                        $sma_sales_items->product_name = $pro_combo->name;
                                        $sma_sales_items->product_type = 'combo';

                                        $sma_sales_items->option_id = var_null;
                                        $price_unit = $pro_combo->price;

                                        $sma_sales_items->net_unit_price =  round($price_unit, 2, PHP_ROUND_HALF_UP);
                                        //validamos que iva va registrado y calculamos
                                        if ($pro_combo->tax_rate == 2) {
                                            $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.10), 2, PHP_ROUND_HALF_UP);
                                            $sma_sales_items->item_tax =  round(($price_unit * 0.10), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                            $sma_sales_items->tax_rate_id =  $pro_combo->tax_rate;
                                            $sma_sales_items->tax =  diez_porciento;
                                        }

                                        if ($pro_combo->tax_rate == 3) {
                                            $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.04), 2, PHP_ROUND_HALF_UP);
                                            $sma_sales_items->item_tax =    round(($price_unit * 0.04), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                            $sma_sales_items->tax_rate_id =  $pro_combo->tax_rate;
                                            $sma_sales_items->tax =  cuatro_porciento;
                                        }

                                        if ($pro_combo->tax_rate == 4) {
                                            $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.21), 2, PHP_ROUND_HALF_UP);
                                            $sma_sales_items->item_tax =   round(($price_unit * 0.21), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                            $sma_sales_items->tax_rate_id =  $pro_combo->tax_rate;
                                            $sma_sales_items->tax =  veintiuno_porciento;
                                        }

                                        $sma_sales_items->discount =  valor_cero;
                                        $sma_sales_items->item_discount =  valor_cero;
                                        $sma_sales_items->sub_total =  $sma_sales_items->unit_price * $value['quantity'];

                                        $sma_sales_items->quantity = $value['quantity'];
                                        $sma_sales_items->warehouse = tienda_dos;
                                        $sma_sales_items->serial_no = string_vacio;
                                        $sma_sales_items->real_unit_price = round($pro_combo->price, 2, PHP_ROUND_HALF_UP);
                                        $sma_sales_items->sale_item_id = var_null;
                                        $sma_sales_items->product_unit_id = valor_uno;
                                        $sma_sales_items->product_unit_code = product_unit_code;
                                        $sma_sales_items->unity_quantity = $value['quantity'];
                                        $sma_sales_items->comment = var_null;
                                        $sma_sales_items->gst = var_null;
                                        $sma_sales_items->cgst = var_null;
                                        $sma_sales_items->sgst = var_null;
                                        $sma_sales_items->igst = var_null;

                                        $id_arr = array();
                                        //Insertamos los items de la venta (sus productos)
                                        $iditem = $sale->InsertItems($sma_sales_items);

                                        array_push($id_arr,$iditem);
                                         if ($sma_sales->sale_status == completed) {
                                        $product_combo = $proif->getCombo($name_vari);

                                        if ($product_combo->num_rows > 0) {
                                            while ($producto_c = $product_combo->fetch_object()) {
                                                $item_combo = $proif->getItemsCombo($producto_c->id);

                                                while ($item = $item_combo->fetch_object()) {
                                                    $procut_i = $proif->getExistProductsCode($item->item_code);
                                                    //creamos el objeto para la tabla de costing
                                                    while($procut_is = $procut_i->fetch_object()){
                                                        $sma_costing = new stdClass();
                                                        $sma_costing->product_id = $procut_is->id;
                                                        $sma_costing->sale_item_id = $id_arr[0];
                                                        $sma_costing->sale_id = $idsales;
                                                        //necesitamos los datos de purchase de cada producto para relacionar con la tabla purchase
                                                        $idpur = $sale->getPurchase($procut_is->id);
    
                                                        if ($idpur->num_rows > 0) {
                                                            while ($purchase = $idpur->fetch_object()) {
                                                                $sma_costing->purchase_item_id = $purchase->id;
                                                                $sma_costing->purchase_net_unit_cost = $purchase->net_unit_cost;
                                                                $sma_costing->purchase_unit_cost = $purchase->unit_cost;
                                                                $sma_costing->sale_net_unit_price =  round($procut_is->price, 2, PHP_ROUND_HALF_UP);;
                                                            }
                                                        } else {
                                                            $sma_costing->purchase_item_id = 0;
                                                            $sma_costing->purchase_net_unit_cost = 0;
                                                            $sma_costing->purchase_unit_cost = 0;
                                                            $sma_costing->sale_net_unit_price =  0;
                                                        }
    
                                                        $sma_costing->quantity = $value['quantity'];

                                                        if ($procut_is->tax_rate == 2) {
                                                            $sma_costing->sale_unit_price =  round($procut_is->price + ($procut_is->price * 0.10), 2, PHP_ROUND_HALF_UP);

                                                        }
                
                                                        if ($procut_is->tax_rate == 3) {
                                                            $sma_costing->sale_unit_price = round($procut_is->price + ($procut_is->price * 0.04), 2, PHP_ROUND_HALF_UP);

                                                        }
                
                                                        if ($procut_is->tax_rate == 4) {
                                                            $sma_costing->sale_unit_price =round($procut_is->price + ($procut_is->price * 0.21), 2, PHP_ROUND_HALF_UP);
                                                        }

                                                        
                                                        //descuento de balance segun el crm
                                                        $sma_costing->quantity_balance = "-".($item->quantity * $value['quantity']) ;
                                                        $sma_costing->inventory = valor_uno;
                                                        $sma_costing->overselling = var_null;
                                                        $sma_costing->option_id =  var_null;
    
                                                        //insertamos el costing
                                                        $iditem = $sale->InsertCosting($sma_costing);
                                                    }
                                                   
                                                }
                                            }
                                        }

                                        //segun estas formas de pago se hace el descuento en las tablas de stock
                                        if ($data[0]['forma de pago'] == 'redsys' || $data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'ptm') {


                                            if ($sma_sales->sale_status == completed) {

                                                if ($pro_combo->type == 'combo') {

                                                        $product_combo = $proif->getCombo($name_vari);


                                                        while ($producto_c = $product_combo->fetch_object()) {

                                                            $item_combo = $proif->getItemsCombo($producto_c->id);

                                                            while ($item = $item_combo->fetch_object()) {


                                                                $procut_i = $proif->getExistProductsCode($item->item_code);

                                                                while ($items_combo = $procut_i->fetch_object()) {
                                                                    $restar = new stdClass();
                                                                    $restar->product_id = $items_combo->id;
                                                                    $restar->quantity = $item->quantity * $value['quantity'];
                                                                    $restar->option_id =  var_null;

                                                                    $result = $sale->restarProduct($restar);
                                                                    $results = $sale->restarProductWarehouse($restar);
                                                                    $resultss = $sale->restarPurchaseItems($restar);
                                                                }
                                                            }
                                                        }
                                                    
                                                }
                                            }
                                        }
                                    }
                                    }
                                }
                            }

                        }
                       //---------------AQUI TERMINA POR CODIGO-VALOR DE OPCION--------------------

                        } else {

                            //si existen
                            while ($productos = $producto->fetch_object()) {
                                $option_id = '';
                                $pricevar = 0;
                                //creamos objeto de items
                                $sma_sales_items = new stdClass();
                                $sma_sales_items->sale_id = $idsales;
                                $sma_sales_items->product_id = $productos->id;
                                $sma_sales_items->product_code = $productos->code;
                                $sma_sales_items->product_name = $productos->name;
                                if ($productos->type == 'combo') {
                                    $sma_sales_items->product_type = 'combo';
                                } else {
                                    $sma_sales_items->product_type = standard;
                                }

                                if (count($value['opciones']) > 0) {
                                    foreach ($value['opciones'] as $option) {
                                        $name_vari = $option['nombre_opcion'] . ' ' . $option['valor_opcion'];
                                    }

                                    $pro_variant = $proif->getProVarPrice(utf8_decode($name_vari), $productos->id);

                                    if ($pro_variant->num_rows > 0) {
                                        while ($prodts = $pro_variant->fetch_object()) {
                                            $pricevar =  $prodts->price;
                                            $option_id = $prodts->id;
                                        }
                                    }
                                }

                                if (empty($option_id)) {
                                    $sma_sales_items->option_id = var_null;
                                } else {
                                    if ($productos->type == 'combo') {
                                        $sma_sales_items->option_id = var_null;
                                    } else {
                                        $sma_sales_items->option_id = $option_id;
                                    }
                                }

                                $price_unit = $productos->price + $pricevar;
                                $sma_sales_items->net_unit_price =   round($price_unit, 2, PHP_ROUND_HALF_UP);

                                //validamos que iva va registrado y calculamos 
                                if ($productos->tax_rate == 2) {
                                    $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.10), 2, PHP_ROUND_HALF_UP);
                                    $sma_sales_items->item_tax =  round(($price_unit * 0.10), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                    $sma_sales_items->tax_rate_id =  $productos->tax_rate;
                                    $sma_sales_items->tax =  diez_porciento;
                                }

                                if ($productos->tax_rate == 3) {
                                    $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.04), 2, PHP_ROUND_HALF_UP);
                                    $sma_sales_items->item_tax =   round(($price_unit * 0.04), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                    $sma_sales_items->tax_rate_id =  $productos->tax_rate;
                                    $sma_sales_items->tax =  cuatro_porciento;
                                }

                                if ($productos->tax_rate == 4) {
                                    $sma_sales_items->unit_price =  round($price_unit + ($price_unit * 0.21), 2, PHP_ROUND_HALF_UP);
                                    $sma_sales_items->item_tax =   round(($price_unit * 0.21), 2, PHP_ROUND_HALF_UP) * $value['quantity'];
                                    $sma_sales_items->tax_rate_id =  $productos->tax_rate;
                                    $sma_sales_items->tax =  veintiuno_porciento;
                                }


                                $sma_sales_items->discount =  valor_cero;
                                $sma_sales_items->item_discount =  valor_cero;
                                if ($pricevar == 0) {
                                    if ($productos->type == 'combo') {
                                        $sma_sales_items->sub_total =  $sma_sales_items->unit_price * $value['quantity'];
                                    } else {
                                        $sma_sales_items->sub_total =  $sma_sales_items->unit_price * $value['quantity'];
                                    }
                                } else {
                                    $sma_sales_items->sub_total =  $sma_sales_items->unit_price * $value['quantity'];
                                }
                                $sma_sales_items->quantity = $value['quantity'];
                                $sma_sales_items->warehouse = tienda_dos;
                                $sma_sales_items->serial_no = string_vacio;
                                $sma_sales_items->real_unit_price = round($productos->price, 2, PHP_ROUND_HALF_UP);
                                $sma_sales_items->sale_item_id = var_null;
                                $sma_sales_items->product_unit_id = valor_uno;
                                $sma_sales_items->product_unit_code = product_unit_code;
                                $sma_sales_items->unity_quantity = $value['quantity'];
                                $sma_sales_items->comment = var_null;
                                $sma_sales_items->gst = var_null;
                                $sma_sales_items->cgst = var_null;
                                $sma_sales_items->sgst = var_null;
                                $sma_sales_items->igst = var_null;

                                $id_arr = array();
                                //Insertamos los items de la venta (sus productos)
                                $iditem = $sale->InsertItems($sma_sales_items);

                                array_push($id_arr,$iditem);

                                if ($productos->type == 'combo') {

                                    if ($sma_sales->sale_status == completed) {
                                        $product_combo = $proif->getCombo($value['codigo']);

                                        if ($product_combo->num_rows > 0) {
                                            while ($producto_c = $product_combo->fetch_object()) {
                                                $item_combo = $proif->getItemsCombo($producto_c->id);

                                                while ($item = $item_combo->fetch_object()) {
                                                    $procut_i = $proif->getExistProductsCode($item->item_code);
                                                    //creamos el objeto para la tabla de costing
                                                    while ($procut_is = $procut_i->fetch_object()) {
                                                        $sma_costing = new stdClass();
                                                        $sma_costing->product_id = $procut_is->id;
                                                        $sma_costing->sale_item_id = $id_arr[0];
                                                        $sma_costing->sale_id = $idsales;
                                                        //necesitamos los datos de purchase de cada producto para relacionar con la tabla purchase
                                                        $idpur = $sale->getPurchase($procut_is->id);
    
                                                        if ($idpur->num_rows > 0) {
                                                            while ($purchase = $idpur->fetch_object()) {
                                                                $sma_costing->purchase_item_id = $purchase->id;
                                                                $sma_costing->purchase_net_unit_cost = $purchase->net_unit_cost;
                                                                $sma_costing->purchase_unit_cost = $purchase->unit_cost;
                                                                $sma_costing->sale_net_unit_price =  round($procut_is->price, 2, PHP_ROUND_HALF_UP);
                                                                ;
                                                            }
                                                        } else {
                                                            $sma_costing->purchase_item_id = 0;
                                                            $sma_costing->purchase_net_unit_cost = 0;
                                                            $sma_costing->purchase_unit_cost = 0;
                                                            $sma_costing->sale_net_unit_price =  0;
                                                        }
    
                                                        $sma_costing->quantity = $value['quantity'];
    
                                                        if ($procut_is->tax_rate == 2) {
                                                            $sma_costing->sale_unit_price =  round($procut_is->price + ($procut_is->price * 0.10), 2, PHP_ROUND_HALF_UP);
                                                        }
            
                                                        if ($procut_is->tax_rate == 3) {
                                                            $sma_costing->sale_unit_price = round($procut_is->price + ($procut_is->price * 0.04), 2, PHP_ROUND_HALF_UP);
                                                        }
            
                                                        if ($procut_is->tax_rate == 4) {
                                                            $sma_costing->sale_unit_price =round($procut_is->price + ($procut_is->price * 0.21), 2, PHP_ROUND_HALF_UP);
                                                        }
    
                                                    
                                                        //descuento de balance segun el crm
                                                        $sma_costing->quantity_balance = "-".($item->quantity * $value['quantity']) ;
                                                        $sma_costing->inventory = valor_uno;
                                                        $sma_costing->overselling = var_null;
                                                        $sma_costing->option_id =  var_null;
    
                                                        //insertamos el costing
                                                        $iditem = $sale->InsertCosting($sma_costing);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    //creamos el objeto para la tabla de costing
                                    $sma_costing = new stdClass();
                                    $sma_costing->product_id = $productos->id;
                                    $sma_costing->sale_item_id = $iditem;
                                    $sma_costing->sale_id = $idsales;
                                    //necesitamos los datos de purchase de cada producto para relacionar con la tabla purchase
                                    $idpur = $sale->getPurchase($productos->id);
                                    if ($idpur->num_rows > 0) {
                                        while ($purchase = $idpur->fetch_object()) {
                                            $sma_costing->purchase_item_id = $purchase->id;
                                            $sma_costing->purchase_net_unit_cost = $purchase->net_unit_cost;
                                            $sma_costing->purchase_unit_cost = $purchase->unit_cost;
                                            $sma_costing->sale_net_unit_price =  $purchase->unit_cost;
                                        }
                                    } else {

                                        $sma_costing->purchase_item_id = 0;
                                        $sma_costing->purchase_net_unit_cost = 0;
                                        $sma_costing->purchase_unit_cost = 0;
                                        $sma_costing->sale_net_unit_price =  0;
                                    }

                                    $sma_costing->quantity = $value['quantity'];

                                    $sma_costing->sale_unit_price = $productos->price;
                                    //descuento de balance segun el crm
                                    $sma_costing->quantity_balance = $productos->quantity - $value['quantity'];
                                    $sma_costing->inventory = valor_uno;
                                    $sma_costing->overselling = var_null;
                                    if (empty($option_id)) {
                                        $sma_costing->option_id =  var_null;
                                    } else {
                                        $sma_costing->option_id =  $option_id;
                                    }
                                    if ($sma_sales->sale_status == completed) {

                                    //insertamos el costing
                                        $iditem = $sale->InsertCosting($sma_costing);
                                    }
                                }
                                //segun estas formas de pago se hace el descuento en las tablas de stock
                                if ($data[0]['forma de pago'] == 'redsys' || $data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'ptm') {

                                    if ($sma_sales->sale_status == completed) {
                                        if ($productos->type == 'combo') {

                                            $product_combo = $proif->getCombo($value['codigo']);

                                            if ($product_combo->num_rows > 0) {
                                                while ($producto_c = $product_combo->fetch_object()) {

                                                    $item_combo = $proif->getItemsCombo($producto_c->id);

                                                    while ($item = $item_combo->fetch_object()) {
                                                        $procut_i = $proif->getExistProductsCode($item->item_code);

                                                        while ($items_combo = $procut_i->fetch_object()) {
                                                            $restar = new stdClass();
                                                            $restar->product_id = $items_combo->id;
                                                            $restar->quantity = $item->quantity * $value['quantity'];
                                                            $restar->option_id =  var_null;

                                                            $result = $sale->restarProduct($restar);
                                                            $results = $sale->restarProductWarehouse($restar);
                                                            $resultss = $sale->restarPurchaseItems($restar);
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            $restar = new stdClass();
                                            $restar->product_id = $productos->id;
                                            $restar->quantity = $value['quantity'];
                                            $restar->option_id =  $option_id;
                                            $result = $sale->restarProduct($restar);
                                            $results = $sale->restarProductWarehouse($restar);
                                            $resultss = $sale->restarPurchaseItems($restar);

                                            if (count($value['opciones']) > 0) {
                                                foreach ($value['opciones'] as $option) {
                                                    $restar->variants = $option['nombre_opcion'] . ' ' . $option['valor_opcion'];
                                                }

                                                $variantp = $sale->restarVariantsProduct($restar);
                                                $restwv = $sale->restarWareHouseProductVariants($restar);
                                            }
                                        }
                                    }

                                    //var_dump($result);
                                    //var_dump($results);
                                    //var_dump($resultss);
                                }
                            }
                        }
                    }

                    //para registrar los pagos validamos los metodos de pago
                    if ($data[0]['forma de pago'] == 'redsys' || $data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'pmt') {
                        $pago = new stdClass();
                        $pago->sale_id = $idsales;
                        //segun estas formas el pago es el total de la venta                                
                        $pago->amount = $data[0]['Totales'][3]['Total con impuestos'];
                        $pago->created_by = $created_by;
                        $pago->type = 'received';

                        //validamos que metodo en la tienda y le damos su equivalencia segun el crm
                        if ($data[0]['forma de pago'] == 'redsys') {
                            $pago->paid_by = 'CC';
                        }

                        if ($data[0]['forma de pago'] == 'pp_standard' || $data[0]['forma de pago'] == 'pmt') {
                            $pago->paid_by = 'otros';
                        }

                        //insertamos pago
                        $idpago = $sale->InsertPago($pago);
                        //var_dump($idpago);
                    }



                    /*if ($data[0]['forma de pago'] == 'cod' || $data[0]['forma de pago'] == 'bank_transfer') {
                $pago = new stdClass();
                $pago->sale_id = $idsales;
                //segun estas formas el pago es el total de la venta                                
                $pago->amount = valor_cero;
                $pago->created_by = valor_dos;
                $pago->type = 'received';
                //validamos que metodo en la tienda y le damos su equivalencia segun el crm
                if ($data[0]['forma de pago'] == 'cod') {
                    $pago->paid_by = 'otros';
                }

                if ($data[0]['forma de pago'] == 'bank_transfer') {
                    $pago->paid_by = 'deposit';
                }
                $idpago =  $sale->InsertPago($pago);
                //var_dump($idpago);
            }*/

                    if ($idpago) {
                        $status = pagado;
                        $paid = $data[0]['Totales'][3]['Total con impuestos'];
                        $result =  $sale->UpdateVentaPago($status, $pago->sale_id, $paid);
                    }
                } else {
                    throw new Exception('No se registro la venta ' . "\n" .  json_encode($data) . "\n" . "-------------" . "\n");
                }
            } else {
                throw new Exception('No se registro la venta ' . "\n" .  json_encode($data) . "\n" . "-------------" . "\n");
            }
        }
    }
} catch (Exception $e) {

    $log->logs('Error capturada: ' . $e->getMessage());
}
