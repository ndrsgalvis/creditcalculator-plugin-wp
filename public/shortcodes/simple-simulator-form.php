<?php

function EnqueueScriptsAndStyles(){
    wp_enqueue_style('style', plugins_url("../assets/style/style.css", __FILE__));
//     wp_enqueue_style('bootstrap', plugins_url("../assets/bootstrap/css/bootstrap.css", __FILE__));
    wp_register_script("simple_credit_simulator", plugins_url("../assets/js/simulator-form.js", __FILE__));
//     wp_register_script("queue_script_bootstrap", plugins_url("../assets/bootstrap/js/bootstrap.js", __FILE__));
    wp_enqueue_script('lottie', plugins_url("../assets/js/lottie-player.js", __FILE__));
}

add_action("wp_enqueue_scripts","EnqueueScriptsAndStyles");

function add_simple_simulator_form(){


    if( isset($_GET['months']) &&
        isset($_GET['typeCredit']) && 
        isset($_GET['mount']) ){
            $mount = $_GET['mount'];
            $months = $_GET['months'];
            $typeCredit = $_GET['typeCredit'];
    }
    
    global $wpdb;

    $tabla = "{$wpdb->prefix}creditos";
    $query = "SELECT * FROM $tabla";
    $lista_creditos = $wpdb->get_results($query,ARRAY_A);
    
    $tabla2 = "{$wpdb->prefix}parametros";
    $query = "SELECT * FROM $tabla2";
    $lista_parametros = $wpdb -> get_results($query, ARRAY_A);

    wp_enqueue_script("simple_credit_simulator");
//     wp_enqueue_script("queue_script_bootstrap");
    wp_enqueue_script("lottie");
//     wp_enqueue_style("bootstrap");
    wp_enqueue_style("style");

    $response = '
		<head>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<haed>
        <div id="full-credit-container">
        <div id="credit-form">
            <h4>Simula tú crédito</h4>
            <form id="validate-form">
                <select id="credit-type" required>
                    <option value="" selected disabled>Qué tipo de crédito necesitas?</option>
                
                    ';
    foreach($lista_creditos as $key => $value){
        if($typeCredit == $value['idCredito']){
				$creditName = $value['nombre'];
                $response .= '<option value="'.$value['interes'].'" selected>'. $value['nombre'] .' </option>';
        }else{
            $response .= '<option value="'.$value['interes'].'">'. $value['nombre'] .' </option>';
        }
    }

    $response .= '
                </select>
                <input placeholder="Cuánto dinero necesitas?"
                    type="text" name="currency-field" id="currency-field" 
                    pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="'.$mount.'" data-type="currency" required>
                <input type="number" id="months" value="'.$months.'" placeholder="Por cuántos meses?" required>
          
                <p align="left" style="font-size: 14px;">
                    * No se inclye el valor de otros cargos 
                    como seguros, contemplados al tomar el credito
                    <br>
                    <br>
                    * No se inclye el valor de otros cargos como seguros, 
                    contemplados al tomar el credito
                </p>
                <button type="button" class="credit-data" id="primary-btn">Simular</button>
            </form>
        </div>
        <div id="credit-form">
            <div id="show-request">
                <h4>Proyección de tu solicitud</h4>
                <div id="cuota">
                    <h5 style="font-size: 16px;">Cuota</h5>
                    <h1 id="mount"></h1>
                    <p style="font-size: 14px;"> Esta sera tú cuota mensual.</p>
                </div>

                <br><hr><br>    
                <p align="left" style="font-size: 14px;">
                    * Plazos de 1 a 72 meses, tasa de intrese fija. Sujeto a estudio de crédito y cumplimiento de las políticas de Finanmoto SAS 
                </p>
                <button type="button" id="primary-btn" class="requestCredit" data-toggle="modal" data-target="#exampleModal">Solicitar</button>
            </div>
            <div id="empty-request">
                <lottie-player src="https://lottie.host/48cef7c6-dc40-48e5-9bf5-5e30be9c5e28/W9pqXZPkk1.json" background="transparent" speed="1" style="width: 100%; height: 300px;" loop autoplay></lottie-player>
                <p style="font-size: 14px;">Visualiza aquí la proyección de tu solicitud</p>
            </div>
        </div>
        
        <div style="margin-top: 30px;" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel" >Resumen de tú proyección</h4>
                </div>
                <div class="modal-body">
                    
                    <table style="font-size:16px; ">
                    <tr>
                        <td>Tasa de interés</td>
                        <td id="modalRate"></td>
                    </tr>
                    <tr>
                        <td>Capital + Interés</td>
                        <td id="modalQuotaRate"></td>
                    </tr>
                    <tr>
                        <td>Cuota mensual</td>
                        <td id="modalMonthQuota"></td>
                    </tr>
                    <tr>
                        <td>Meses</td>
                        <td id="modalMonth"></td>
                    </tr>
                    </table>
                    
                    <table style="font-size:16px;">
                    ';
            foreach($lista_parametros as $key => $value){
                $response .= '
                    <tr>
                        <td>'.$value['nombre'].'</td>
                        <td>'.$value['valor'].'</td>
                    </tr>
                ';
            }        

            $response .='
                    </table>

                    <p style="white-space: pre-line; font-size:16px;">
                        Una vez aprobada tu solicitud, el desembolso del crédito puede tarder hasta 2 días hábiles.

                        Estos valores corresponden al pago normal del crédito bajo las condiciones simuladas, pueden cambiar si el pago entra en mora o se realizán pagos distintos a los pactados. (Abonos, anticipos y total)
                    </p>
                </div>
                <div class="modal-footer">
                    <h5 class="modal-title">¿Desea continuar?</h5>
                    <div>
                        <button type="button" id="cancel-btn" data-dismiss="modal">Cancelar</button>
                        <a align="center" class="modalContinue" id="primary-btn"> Sí</a>
                    </div>
                </div>
            </div>
            </div>
        </div>   
        </div>
		<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    ';
    return $response;
}

add_shortcode("simple_credit_simulator","add_simple_simulator_form");












function add_form_simulator_simple(){


    if( isset($_GET['months']) &&
        isset($_GET['typeCredit']) && 
        isset($_GET['mount']) ){
            
    }
    
    global $wpdb;

    $tabla = "{$wpdb->prefix}creditos";
    $query = "SELECT * FROM $tabla";
    $lista_creditos = $wpdb->get_results($query,ARRAY_A);

    wp_enqueue_script("simple_credit_simulator");
//     wp_enqueue_script("queue_script_bootstrap");
//     wp_enqueue_style("bootstrap");
    wp_enqueue_style("style");

    $response = '
        <div id="full-credit-container">
        <div id="credit-form">
            <h4>Simula tú crédito</h4>
            <form id="validate-form">
                <select id="credit-type" required>
                    <option value="" disabled selected>Qué tipo de crédito necesitas?</option>
                
                    ';
    foreach($lista_creditos as $key => $value){
            $response .= '<option value="'.$value['idCredito'].'">'. $value['nombre'] .' </option>';
    }

    $response .= '
                </select>
                <input placeholder="Cuánto dinero necesitas?"
                    type="text" name="currency-field" id="currency-field" 
                    pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" required>
                <input type="number" id="months" placeholder="Por cuántos meses?" required>
           
                <p align="left" style="font-size: 14px;">
                    * No se inclye el valor de otros cargos 
                    como seguros, contemplados al tomar el credito
                    <br>
                    <br>
                    * No se inclye el valor de otros cargos como seguros, 
                    contemplados al tomar el credito
                </p>
                <a class="credit-data-simple" id="primary-btn">Simular</a>
            </form>
        </div>
    </div>
    ';
    return $response;
}

add_shortcode("simple_credit_simulator_2","add_form_simulator_simple");