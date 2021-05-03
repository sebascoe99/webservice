@extends('includes.dash')


@section('contenido')

<script type="text/javascript">
    $( window ).load(function() {

        window.onload = ocultarCombo();
        window.onload = ocultarLink();
    });


        function ocultarCombo(){
        document.getElementById('primary').style.display = 'none';
        }

        function ocultarLink(){
        document.getElementById('obj3').style.display = 'none';
        }

        $(document).ready(function()
        {
            $("#save_task").click(function() {

                idCombo = $("#id_combo2").val();
                link = $("#texto_footer").val();
                //alert(link );

                if(idCombo == "select"){
                    return alert("Debe ingresar el lugar donde estará la imagen");
                }

                if(link == null || link == ""){
                    return alert("Debe ingresar el link correspondiente a la imagen");
                }

                var form = document.forms.namedItem("formMagento"); // high importance!, here you need change "yourformname" with the name of your form
                //var id = form.id_combo2.value
                var formdata = new FormData(form); // high importance

                $.ajax({
                    url: './metodoMagento',
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

    });

</script>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 align="right">IMAGENES</h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>


    <div class="container p-4">
        <div class="row-center">
            <div class="col-md-5.5">
                <div class="card card-body">
                    <form name="formMagento" id="formMagento" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                                Ingresa el archivo:
                                <input id="imagen" name="imagen" type="file" accept="image/png, image/jpeg"/>
                        </div>


                       <div class="form-group">
                            <select id="id_combo2" name="id_combo2" class="form-control">
                                <option id = "option1" value="select" selected>- Seleccione en que lugar ira la imagen -</option>
                            </select>
                        </div>

                        <div id="obj3" class="form-group">
                            <input type="text" id="texto_footer" name="texto_footer" class="form-control"
                                placeholder="Ingrese el link con relacion a la imagen" autofocus>
                        </div>

                        <div class="form-group">
                            <select id="primary" name="contexto" class="form-control">
                                <option value="1">Interno</option>
                                <option value="2">Externo</option>
                            </select>
                        </div>


                        <input type="button" class="btn btn-success btn-block" name="save_task"
                               id="save_task" value="Guardar">
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

