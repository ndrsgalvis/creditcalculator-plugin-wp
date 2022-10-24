<?php
    global $wpdb;

    $tabla = "{$wpdb->prefix}creditos";
    $tabla2 = "{$wpdb->prefix}parametros";

    if(isset($_POST['saveCredit'])){
        $creditName = $_POST['creditName'];
        $rateValue = $_POST['rateValue'];
        $datos = [ 'idCredito' => null, 'nombre' => $creditName,  'interes' => $rateValue, ];
        $wpdb->insert($tabla,$datos);
    }

    if(isset($_POST['saveParams'])){
        $paramName = $_POST['paramName'];
        $value = $_POST['value'];
        $datos2 = [ 'idParametro' => null, 'nombre' => $paramName,  'valor' => $value, ];
        $wpdb->insert($tabla2,$datos2);
      }
      
      if(isset($_POST['updateData'])){
        
        $paramId = $_POST['modalId'];
        $control = trim($_POST['modalControl']);
        $paramName = $_POST['modalParam'];
        $paramValue = $_POST['modalValue'];

        if($control === 'c'){
          $where = [ 'idCredito' => $paramId ];
          $datos = [ 'nombre' => $paramName,  'interes' => doubleval(trim($paramValue)), ];
          $wpdb->update($tabla, $datos, $where);
        }else if($control === 'p'){
          $where = [ 'idParametro' => $paramId ];
          $datos = [ 'nombre' => $paramName,  'valor' => doubleval(trim($paramValue)), ];
          $respuesta = $wpdb->update($tabla2, $datos, $where);
        }
    }

    $query = "SELECT * FROM $tabla";
    $lista_creditos = $wpdb->get_results($query,ARRAY_A);
    if(empty($lista_creditos)){
        $lista_creditos = array();
    }
  
    $query2 = "SELECT * FROM $tabla2";
    $lista_params = $wpdb->get_results($query2,ARRAY_A);
    if(empty($lista_params)){
        $lista_params = array();
    }
    
 ?>
<style>
  .container{
    width: 100%;
    display: flex;
    justify-content: space-between;
  }
  .container h1{
    margin-bottom: 20px;
  }
  .container div form{
    margin-bottom: 20px;
  }
  .container div:nth-child(1){
    margin: 20px;
  }
  .container div:nth-child(2){
    margin: 20px;
  }

</style>  
 <div class="wrap">
        <?php
             echo "<h1 class='wp-heading-inline' style='margin-bottom: 25px;'>" . get_admin_page_title() . "</h1>";
        ?>

        <div class="container">
          <div>
            <h1>Listado de Créditos</h1>
            <form method="post" id="formParams">
                <label for="creditName"> Crédito</label> 
                <input type="text" name="creditName" placeholder="Nombre del crédito">
                <label for="rateVlaue"> Interés </label> 
                <input type="number" name="rateValue" step="0.001">
                <button type="submit" class="btn btn-primary" name="saveCredit" id="saveCredit">Guardar</button>
            </form>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <th >Créditos</th>
                    <th >Interés</th>
                    <th >Acciones</th>
                </thead>
                <tbody id="the-list">
                    <?php 
                        foreach ($lista_creditos as $key => $value) {
                          $id = $value['idCredito'];
                          $nameCredit = $value['nombre'];
                          $rateValue = $value['interes'];
                            echo "
                                <tr>
                                    <td data-control='c' style='display:none;'> c </td>
                                    <td data-name='$nameCredit'>$nameCredit</td>
                                    <td data-value='$rateValue'>$rateValue </td>
                                    <td>
                                      <a data-ver='$id' class='page-title-action'> Editar </a>
                                      <a data-id='$id' class='page-title-action'>Borrar</a>
                                    </td>
                                </tr>
                            ";
                        }

                    ?>
                </tbody>
            </table>
          </div>
        
          <div>
          <h1>Listado de Parámetros</h1>
            <form method="post">
              <label for="creditName"> Parámetro</label>
              <input type="text" name="paramName" placeholder="Nombre del parámetro">
              <label for="rateVlaue"> Valor </label>
              <input type="text" name="value" >
              <button type="submit" class="btn btn-primary" name="saveParams" id="saveParams">Guardar</button>
            </form>

            <table class="wp-list-table widefat fixed striped pages">
                    <thead>
                        <th >Parametros</th>
                        <th >Valores</th>
                        <th >Acciones</th>
                    </thead>
                    <tbody id="the-list">
                        <?php 
                            foreach ($lista_params as $key => $value) {
                              $id = $value['idParametro'];
                              $nombre = $value['nombre'];
                              $valor = $value['valor'];
                              echo "
                                    <tr>   
                                        <td data-control='p' style='display:none;'> p </td>
                                        <td data-name='$nombre' >$nombre</td>
                                        <td data-value='$valor' > $valor </td>
                                        <td>
                                          <a data-ver='$id' id='editar' class='page-title-action'>Editar</a>
                                          <a data-idp='$id' class='page-title-action'>Borrar</a>
                                        </td>
                                    </tr>
                                ";
                            }
                        ?>
                    </tbody>
            </table>
          </div>
        </div>                   
 </div>

<!-- creditos -->
<div class="modal fade" id="modalnuevo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLongTitle">Nueva encuesta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
          

    </div>
  </div>
</div>

<!-- parametros -->
<div class="modal fade" id="modalestadisticas" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLongTitle">Actualizar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form method="post">
          <label for="creditName"> Parametro / Crédito</label>
          <input type="text" name="modalParam" id="modalParam">
          <label for="rateVlaue"> Valor </label>
          <input type="text" name="modalValue" id="modalValue">
          <input type="text" name="modalControl" id="modalControl" style="display:none;">
          <input type="text" name="modalId" id="modalId" style="display:none;">
          <button type="submit" class="btn btn-primary" name="updateData" id="12">Actualizar</button>
        </form>
      </div>
    </div>
  </div>
</div>