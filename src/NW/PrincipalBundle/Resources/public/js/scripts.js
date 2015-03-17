// As the page loads, call these scripts
jQuery(document).ready(function($) {
    
    /* Iniciar carrouseles */
    $('#bannersCarousel').carousel();

	// Link en una fila de una tabla
	$(".clickableRow").click(function() {
	    window.document.location = $(this).attr("href");
	});

	// Script que habilita/deshabilita edición de texto para la iglesia
	$(".boton-editar").click(function(){
		$(".form-form").toggleClass("invisible");
		$(".texto-form").toggleClass("invisible");
	});

	// Script que muestra descripción en modal de tareas del calendario de novios
	$('#tablaTareas').on('click', '.tarea_link', function() {
		
		var mostrar = true;
		if(!$("#tarea_selected_"+$(this).attr("id")).hasClass("invisible"))
		{
			mostrar = false;
		}

		// Ocultar todo
		$(".desaparecible").addClass("invisible");

		if(mostrar)
		{
			$("#tarea_selected_"+$(this).attr("id")).removeClass("invisible");
			$("#tarea_descripcion_"+$(this).attr("id")).removeClass("invisible");
		}
	});

	// En el registro de los novios, el checkbox de mismos datos copia los datos de la novia y los pega en el de novios
	$('input[name="registro[mismaDireccion]"]').change(function(){
		if($(this).is(':checked')){
			$("#registro_novios_lada").val($("#registro_novias_lada").val());
			$("#registro_novios_telefono").val($("#registro_novias_telefono").val());
			$("#registro_novios_direccion").val($("#registro_novias_direccion").val());
			$("#registro_novios_estado").val($("#registro_novias_estado").val());
			$("#registro_novios_ciudad").val($("#registro_novias_ciudad").val());
			$("#registro_novios_cp").val($("#registro_novias_cp").val());
		}
		else
		{
			$("#registro_novios_lada").val('');
			$("#registro_novios_telefono").val('');
			$("#registro_novios_direccion").val('');
			$("#registro_novios_estado").val('');
			$("#registro_novios_ciudad").val('');
			$("#registro_novios_cp").val('');
		}
	});

	$(".hacerTooltip").tooltip(); 

}); /* end of as page load scripts */