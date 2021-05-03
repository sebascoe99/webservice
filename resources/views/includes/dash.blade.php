
@include('includes.scripts')

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Blank Page</title>

    <script type="text/javascript">


        $(document).ready(function(){
            //Consumo combo tipo archivo
            $.ajax({
                data:"",
                url: "{{ url('comboWordPress') }}",
                type: "get",
                dataType: "json",
                success: function(data){
                    $.each(data, function(index, value) {
                        //Crear el combo dinamico
                        var id   = value.id;
                        var tipo = value.tipo;
                        $("#id_combo").append("<option value='"+id+"'>"+tipo+"</option>");
                    });
                },
                error: function(error){
                    console.log(error);
                }
            });


            $.ajax({
                data:"",
                url: "{{ url('categoriaAll2') }}",
                type: "get",
                dataType: "json",
                success: function(data){
                    $.each(data, function(index, value) {
                        //Crear el combo dinamico
                        var name   = value.categoryName;
                        var id = value.categoryId;
                        //console.log(id);
                        $("#secundary").append("<option value='"+id+"'>"+name+"</option>");
                    });
                },
                error: function(error){
                    console.log(error);
                }
            });


            $.ajax({
                data:"",
                url: "{{ url('comboMagento') }}",
                type: "get",
                dataType: "json",
                success: function(data){
                    $.each(data, function(index, value) {
                        //Crear el combo dinamico
                        var id   = value.id;
                        var tipo = value.tipo;
                        $("#id_combo2").append("<option value='"+id+"'>"+tipo+"</option>");
                    });
                },
                error: function(error){
                    alert(error);
                }
            });

         });


        $(document).ready(function(){
        $("#id_combo").change(function () {
             var end = this.value;
            //console.log($('#id_combo').val());

                if (($('#id_combo').val() == 3) || ($('#id_combo').val() == 4)) {
                    document.getElementById('obj1').style.display='block'
                }else{
                    document.getElementById('obj1').style.display='none'
                }

            });


            $("#id_combo2").change(function () {
             var end = this.value;
            //console.log($('#id_combo').val());

                if (($('#id_combo2').val() == 6) || ($('#id_combo2').val() == 7) || ($('#id_combo2').val() == 8) || ($('#id_combo2').val() == 9)) {
                    document.getElementById('obj3').style.display='block'
                }else{
                    document.getElementById('obj3').style.display='none'
                }

            });

         });


        </script>


  @include('includes.styles')


</head>
<body class="hold-transition sidebar-mini">

<!-- Site wrapper -->
<div class="wrapper">



 @include('includes.navbar')


  <!-- Main Sidebar Container -->
  @include('includes.sidebar')

  @yield('contenido')


  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<script>
	$(document).ready(function()
        {
			$.fn.modal.Constructor.prototype._enforceFocus = function() {
			  $(document).off('focusin.bs.modal').on('focusin.bs.modal', $.proxy((function(e) {
				if (this.$element[0] !== e.target && !this.$element.has(e.target).length && !$(e.target).closest('.cke_dialog, .cke').length) {
				  this.$element.trigger('focus');
				}
			  }), this));
			};
		}
	);
</script>

</body>

</html>
