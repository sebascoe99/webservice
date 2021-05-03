@extends('includes.dash')

@section('contenido')

<link rel="stylesheet" type="text/css" href="{{ url('assets/css/jquery.dataTables.min.css') }}">
<!-- buttons data table  -->


<script src="{{ url('assets/ckeditor/ckeditor.js') }}"></script>
    <script>


        $(window).on('load', function() {
                    CKEDITOR.config.height = 200;
                    CKEDITOR.config.width = 'auto';

                    CKEDITOR.replace('bodyMarca');

                    CKEDITOR.replace('bodyEditarMarca');

        });


        $(document).ready(function(){

        function eliminarMarca(rowData){
                $.ajax({
                        url: './eliminarMarca',
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
            $(window).scrollTop(y+450);
         }



           $('#dtMarca').DataTable( {

                "emptyrecords": 'No records to display',
                "ajax": './llamarMarca',
                "columns": [
                    { "data": "id" },
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


                var table = $('#dtMarca').DataTable();

                $('#dtMarca tbody').on( 'click', 'tr', function () {
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
                });

                $("#cerrarModal2").click(function(){
                     $("#myModal2").modal('hide')
                });


                $('#buttonRemove').click( function () {
                    //table.row('.selected').remove().draw( false );
                    var rowData = table.row('.selected').data()['id'];
                    eliminarMarca(rowData);
                    //alert(rowData);
                    //table.row('.selected')
                });

                $('#buttonEdit').click( function(){
                    var id = table.row('.selected').data()['id'];
                    var descripcion = table.row('.selected').data()['descripcion'];
                    //alert(id+descripcion+anios);
                    $("#idMarca").val(id);
                    CKEDITOR.instances['bodyEditarMarca'].setData(descripcion);
                    //$("#bodyEditar").(descripcion);
                    $('#myModal').modal('show');
                });

                $("#cerrarModal").click(function(){
                     $("#myModal").modal('hide')
                });




            $("#save_task").click(function() {

                if(CKEDITOR.instances['bodyMarca'].getData()==null || CKEDITOR.instances['bodyMarca'].getData().length == 0)
                {
                    return alert("Debe ingresar la descripcion de la marca!");
                }

                var form = document.forms.namedItem("formMarca"); // high importance!, here you need change "yourformname" with the name of your form
                var formdata = new FormData(form); // high importance

                formdata.append('contenido', CKEDITOR.instances['bodyMarca'].getData());

                //console.log(CKEDITOR.instances['bodyMarca'].getData());
                //return false;

                $.ajax({
                    url: './insertarMarca',
                    type: "POST",
                    dataType: "json",
                    contentType: false,
                    data: formdata, // high importance!
                    processData: false, // high importance!
                    success: function(result)
                    {
                        alert(result);
                        //console.log(result);
                        location.reload();

                    },
                    error: function(msg){
                        console.log(msg)
                    }
                });
            });




            $("#guardarEditar").click(function() {

                var idMarca = $("#idMarca").val();

                var bodyEditar = CKEDITOR.instances['bodyEditarMarca'].getData();
                //console.log(idMarca + bodyEditar);

                if(bodyEditar.length == 0){
                    return alert("Debe ingresar la descripcion de la garantia!");
                }
                //console.log(idGarantia, bodyEditar, anios);
                $.ajax({
                    url: './actualizarMarca',
                    type: "POST",
                    dataType: "json",
                    data: { idMarca: idMarca, bodyEditar: bodyEditar}, // high importance!
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
            <input type="hidden" id="idMarca"/>

            <div class="form-group">
                <textarea id="bodyEditarMarca" name="bodyEditarMarca">

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
                    <h1 align="right">MARCA</h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>


    <div class="container p-4">
        <button id="buttonCreate" class="btn btn-success">Crear</button>
        <button id="buttonEdit" class="btn btn-primary">Editar</button>
        <button id="buttonRemove" class="btn btn-danger">Eliminar</button>
    </div>




    <div class="modal" tabindex="-1" role="dialog" id="myModal2" >
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">

            <form name="formMarca" id="formMarca" method="post" enctype="multipart/form-data">

                <div class="modal-header">
                    <button type="button" id= "cerrarModal2" class="btn btn-danger">Cerrar</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="idMarca"/>

                    <div class="form-group">
                        <textarea id="bodyMarca" name="bodyMarca">

                        </textarea>
                    </div>

                </div>

                <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save_task">Guardar</button>
                </div>
            </form>

          </div>
        </div>
      </div>




        <div class="container p-4">
            <div class="row-center">
                <div class="col-md-5.5">
                    <div class="card card-body">
                        <table id="dtMarca" class="display" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripcion</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>

</div>

@endsection

