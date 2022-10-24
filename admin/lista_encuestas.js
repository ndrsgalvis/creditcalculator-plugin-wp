jQuery(document).ready(function($){

    $(document).on('click',"a[data-id]",function(){
        var id = this.dataset.id;
        var url = SolicitudesAjax.url;
        $.ajax({
            type: "POST",
            url: url,
            data:{
                action : "peticioneliminar",
                nonce : SolicitudesAjax.seguridad,
                id: id,
            },
            success:function(){
                alert("Datos borrados");
                location.reload();
            }
        });
    });

    $(document).on('click',"a[data-idp]",function(){
        var id = this.dataset.idp
        var url = SolicitudesAjax.url;
        $.ajax({
            type: "POST",
            url: url,
            data:{
                action : "peticioneliminarparams",
                nonce : SolicitudesAjax.seguridad,
                id: id,
            },
            success:function(){
                alert("Datos borrados");
                location.reload();
            }
        });
    });

    $(document).on('click',"a[data-ver]",function(){
        $("#modalestadisticas").modal("show")

        var id = this.dataset.ver;
        var fila = $(this).closest("tr")
        var param = fila.find("td[data-name]").text();
        var value = fila.find("td[data-value]").text();
        var validate = fila.find("td[data-control]").text();

        $("#modalId").val(id);
        $("#modalParam").val(param);
        $("#modalValue").val(value);
        $("#modalControl").val(validate);
    })




});