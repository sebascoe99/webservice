@extends('includes.dash')

@section('contenido')

<link rel="stylesheet" type="text/css" href="{{ url('assets/css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('assets/css/utilitarios.css') }}">
<!-- buttons data table  -->


<script src="{{ url('assets/ckeditor/ckeditor.js') }}"></script>

    <script>


        $(window).on('load', function() {
                    CKEDITOR.config.height = 200;
                    CKEDITOR.config.width = 'auto';

                    CKEDITOR.replace('body');

                    CKEDITOR.replace('bodyEditar');
        });

        $(document).ready(function()
        {


        //EDITAR -Validacion Años garantida

        $("#anios").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                //display error message
                $("#errmsg").html("Solo numero").show().fadeOut("slow");
                    return false;
            }
        });

        function deleteGarantia(rowData){
                $.ajax({
                        url: './delete',
                        type: "POST",
                        data: { 'id' :rowData }  ,
                        dataType: 'JSON',
                        success: function(result)
                        {
                            alert(result);
                            location.reload();

                        },
                        error: function(msg){
                            console.log(msg)
                        }
                    });

         }


         function crearGarantiaScrollDown(){
            var y = $(window).scrollTop();  //your current y position on the page
            $(window).scrollTop(y+750);
         }

            /*$('#dtGarantias').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            } );*/



          /* $('#dtGarantias').DataTable( {

                "emptyrecords": 'No records to display',
                "ajax": './llamarGarantias',
                "columns": [
                    { "data": "id" },
                    { "data": "titulo" },
                    { "data": "descripcion" }
                ]
            } );*/


                var table = $('#dtGarantias').DataTable( {
                    "emptyrecords": 'No records to display',
                    "ajax": './llamarGarantias',
                    "columns": [
                        { "data": "id" },
                        { "data": "titulo" },
                        {
                            data: 'descripcion',
                            render: function(data, type, row, meta) {
                                //var tamDescripcion = data.length;
                                var data = data.replace(/<[^>]*>?/g, '');
                                return type === 'display' ? data.substr(0,150) : data;


                            }
                        }
                    ],
                    language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
                    }

                } );


                $('#dtGarantias tbody').on( 'click', 'tr', function () {
                    if ( $(this).hasClass('selected') ) {
                        $(this).removeClass('selected');
                    }
                    else {
                        table.$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                    }
                } );

                $('#buttonCreate').click( function () {
                    $('#myModal2').modal('show');
                    //myModal2
                });

                $('#buttonRemove').click( function () {
                    //table.row('.selected').remove().draw( false );
                    var rowData = table.row('.selected').data()['id'];
                    deleteGarantia(rowData);
                    //alert(rowData);
                    //table.row('.selected')
                });

                $('#buttonEdit').click( function(){
                    var id = table.row('.selected').data()['id'];
                    var titulo = table.row('.selected').data()['titulo'];
                    var descripcion = table.row('.selected').data()['descripcion'];

                    //alert(id+descripcion+anios);
                    $("#idGarantia").val(id);
                    $("#titulo").val(titulo);
                    CKEDITOR.instances['bodyEditar'].setData(descripcion);
                    $('#myModal').modal('show');
                });

                $("#cerrarModal").click(function(){
                     $("#myModal").modal('hide')
                });

                $("#cerrarModal2").click(function(){
                     $("#myModal2").modal('hide')
                });


            $("#save_task").click(function() {

                if(CKEDITOR.instances['body'].getData()==null ||
                    CKEDITOR.instances['body'].getData().length == 0)
                {
                    return alert("Debe ingresar la descripcion de la garantia!");
                }

                var form = document.forms.namedItem("formGarantia"); // high importance!, here you need change "yourformname" with the name of your form
                var formdata = new FormData(form); // high importance

                formdata.append('contenido', CKEDITOR.instances['body'].getData());

                $.ajax({
                    url: './metodoGarantia',
                    type: "POST",
                    dataType: "json",
                    contentType: false,
                    data: formdata, // high importance!
                    processData: false, // high importance!
                    success: function(result)
                    {
                        alert(result);
                        location.reload();

                    },
                    error: function(msg){
                        console.log(msg)
                    }
                });
            });




            $("#guardarEditar").click(function() {

                var idGarantia = $("#idGarantia").val();

                var titulo = $("#titulo").val();

                if(titulo.length == 0){
                    return alert("Debe ingresar el titulo");
                }

                var bodyEditar = CKEDITOR.instances['bodyEditar'].getData();

                if(bodyEditar.length == 0){
                    return alert("Debe ingresar la descripcion de la garantia!");
                }

                //console.log(idGarantia, bodyEditar, anios);
                $.ajax({
                    url: './metodoGarantiaEditar',
                    type: "POST",
                    dataType: "json",
                    data: { idGarantia: idGarantia, titulo: titulo, bodyEditar: bodyEditar}, // high importance!
                    success: function(result)
                    {
                        alert(result);
                        location.reload();

                    },
                    error: function(msg){
                        console.log(msg)
                    }
                });
            });

        });



    </script>



<div class="modal" tabindex="-1" role="dialog" id="myModal" >
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title">Editar</h5>
            <button type="button" id= "cerrarModal" class="btn btn-danger">Cerrar</button>
        </div>

        <div class="modal-body">
            <input type="hidden" id="idGarantia"/>

            <div class="form-group purple-border">
                <label for="exampleFormControlTextarea4">Titulo</label>
                <input type="text" class="form-control" id="titulo"/>&nbsp;<span id="errmsg"></span>
            </div>

            <div class="form-group">
                <textarea id="bodyEditar" name="body">

                </textarea>
            </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="guardarEditar">Guardar</button>
        </div>

      </div>
    </div>
  </div>


<!-- if end if-->

<!-- php end -->
<!-- jQuery library -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 align="right">GARANTIA</h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>


    <div class="container p-4">
        <button id="buttonCreate" class="btn btn-success">Crear</button>
        <button id="buttonEdit" class="btn btn-primary">Editar</button>
        <button id="buttonRemove" class="btn btn-danger">Eliminar</button>
    </div>

        <div class="container p-4">
            <div class="row-center">
                <div class="col-md-5.5">
                    <div class="card card-body">
                        <table id="dtGarantias" class="display" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titulo</th>
                                    <th>Descripcion</th>

                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>


        <div class="modal" tabindex="-1" role="dialog" id="myModal2" >
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">

                    <div class="container p-4">

                                    <form name="formGarantia" id="formGarantia" method="post" enctype="multipart/form-data">

                                        <div class="form-group">
                                            <button type="button" id= "cerrarModal2" class="btn btn-danger">Cerrar</button>
                                        </div>

                                        <div class="form-group">
                                            <label for="titulo">Titulo:</label>
                                            <input type="text" id="titulo" name="titulo"><br>
                                        </div>

                                        <div class="form-group">
                                            <textarea id="body" name="body">

                                            </textarea>
                                        </div>

                                        <input type="button"  class="btn btn-success btn-block" name="save_task"
                                            id="save_task" value="Guardar">

                                    </form>
                    </div>
                </div>
            </div>
        </div>

</div>

@endsection

