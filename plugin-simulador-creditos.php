<?php 

/**
 * Plugin Name:       Simulador de creditos
 * Plugin URI:        https://wiedii.co
 * Description:       Formulario para simulación de créditos financieros
 * Version:           1.10.3
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Wiedii
 * Author URI:        https://wiedii.co
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       yardSale
 */


//  shortcodes
require_once plugin_dir_path(__FILE__)."/public/shortcodes/simple-simulator-form.php";

function Activar(){
    global $wpdb;

    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}creditos(
        `idCredito` INT NOT NULL AUTO_INCREMENT,
        `nombre` VARCHAR(45) NULL,
        `interes` DOUBLE NULL,
        PRIMARY KEY (`idCredito`)
    );";
    $wpdb->query($sql);

    $sql1 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}parametros(
        `idParametro` INT NOT NULL AUTO_INCREMENT,
        `nombre` VARCHAR(45) NULL,
        `valor` DOUBLE NULL,
        PRIMARY KEY (`idParametro`) 
        );";
    $wpdb->query($sql1);

}

function Desactivar(){
    flush_rewrite_rules();
}

register_activation_hook(__FILE__,'Activar');
register_deactivation_hook(__FILE__,'Desactivar');

function MainMenu(){
    add_menu_page(
        'Simulador de créditos Pick',
        'Simulador Pick',
        'manage_options',
        plugin_dir_path(__FILE__).'admin/ajustes-simulador.php',
        null,
        plugin_dir_url(__FILE__).'admin/img/pick-ico.svg',
        '1',
    );
}

add_action('admin_menu','MainMenu');


function EncolarJS($hook){
    if($hook != "simulador-creditos/admin/ajustes-simulador.php"){
        return ;
    }
    wp_enqueue_script('JsExterno',plugins_url('admin/lista_encuestas.js',__FILE__),array('jquery'));
    wp_localize_script('JsExterno','SolicitudesAjax',[
        'url' => admin_url('admin-ajax.php'),
        'seguridad' => wp_create_nonce('seg')
    ]);
}
add_action('admin_enqueue_scripts','EncolarJS');


//encolar bootstrap
function EncolarBootstrapJS($hook){
    if($hook != "simulador-creditos/admin/ajustes-simulador.php"){
        return ;
    }
    wp_enqueue_script('bootstrapJs',plugins_url('public/assets/bootstrap/js/bootstrap.min.js',__FILE__),array('jquery'));
}
add_action('admin_enqueue_scripts','EncolarBootstrapJS');


function EncolarBootstrapCSS($hook){
    if($hook != "simulador-creditos/admin/ajustes-simulador.php"){
        return ;
    }
    wp_enqueue_style('bootstrapCSS',plugins_url('public/assets/bootstrap/css/bootstrap.min.css',__FILE__));
}
add_action('admin_enqueue_scripts','EncolarBootstrapCSS');



function EliminarCreditos(){
    $nonce = $_POST['nonce'];
    if(!wp_verify_nonce($nonce, 'seg')){
        die('No tiene acceso para ejecutar esta accion');
    }
    global $wpdb;
    $id = $_POST['id'];
    $tabla = "{$wpdb->prefix}creditos";
    $wpdb->delete($tabla,array('idCredito' =>$id));
    return true;
}

add_action('wp_ajax_peticioneliminar','EliminarCreditos');

function EliminarParametros(){
    $nonce = $_POST['nonce'];
    if(!wp_verify_nonce($nonce, 'seg')){
        die('No tiene acceso para ejecutar esta accion');
    }
    global $wpdb;
    $id = $_POST['id'];
    $tabla = "{$wpdb->prefix}parametros";
    $wpdb->delete($tabla,array('idParametro' =>$id));
    return true;
}

add_action('wp_ajax_peticioneliminarparams','EliminarParametros');
