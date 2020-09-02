var tabla;

// Funcion que se ejecuta al inicio 
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit",function(e) {
        guardaryeditar(e);
    })

    // cargamos los items al select categoria
    $.post("../ajax/articulo.php?op=selectCategoria", function(r)
    {
        // datos que devuelvo a la vista
        $("#idcategoria").html(r);
        $("#idcategoria").selectpicker('refresh');
    })

}

// Funcion limpiar 
function limpiar() {
    // el '#nombre' se refiere al objeto del formulario html por su id.
    // funcion val, devolvera un valor vacio. 
    $("#codigo").val("");
    $("#nombre").val("");  
    $("#descripcion").val("");
    $("#stock").val("");
}

// Funcion mostrar formulario
function mostrarform(flag) {
    limpiar();
    if(flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled",false);
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
    }
}

// Funcion cancelar form
function cancelarform() {
    limpiar();
    mostrarform(false);
}

// Funcion listar
function listar() { 
    tabla=$('#tbllistado').dataTable(
        {
        "aProcessing": true,    // Activamos el procesamiento del datatables
        "aServerSide": true,    // Paginacion y filtrado realizados por el servidor
        dom: 'Bfrtip',          // Definimos los elementos del control de tabla
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdf'
        ],
        "ajax": {
            url: '../ajax/articulo.php?op=listar',
            type: "get",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5,            // paginacion
        "order": [[ 0, "desc" ]]        // orden de datos: columna, orden
    }).DataTable(); 
}

function guardaryeditar(e) {
    e.preventDefault();     // no se activara la accion predeterminada del evento 
    $("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/articulo.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        
        success: function(datos) {
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        } 
    });
    limpiar();
}

function mostrar(idarticulo) {
    // envio por post, al controlador ajax 
    $.post("../ajax/articulo.php?op=mostrar", {idarticulo : idarticulo}, function(data, status)
    {
        data = JSON.parse(data);
        mostrarform(true);

        // datos que devuelvo a la vista
        $("#idcategoria").val(data.idcategoria);
        $("#codigo").val(data.codigo);
        $("#nombre").val(data.nombre);
        $("#stock").val(data.stock);
        $("#descripcion").val(data.descripcion);
        $("#idarticulo").val(data.idarticulo);
    })
}

// Funcion para desactivar registros
function desactivar(idarticulo) {
    bootbox.confirm("¿Está seguro de desactivar el Artículo?",function(result){
        if (result) {
            $.post("../ajax/articulo.php?op=desactivar", {idarticulo : idarticulo}, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            });    
        }
    })
}

// Funcion para activar registros
function activar(idarticulo) {
    bootbox.confirm("¿Está seguro de activar el Artículo?",function(result){
        if (result) {
            $.post("../ajax/articulo.php?op=activar", {idarticulo : idarticulo}, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            });    
        }
    })
}

init();