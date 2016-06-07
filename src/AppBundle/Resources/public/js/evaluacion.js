
//evento para cargar los subtemas de una unidad seleccionada
$(document).ready(function(){
	
	//funciones ejecutadas al cargar el DOM
	//ajax(creatObjAjax("teorico", $("#{{ form.asignatura.vars.id }}").val()));
	$("#aviso").hide();
	$("#aviso2").hide();
	
	$("#unidades").on( 'change', ".checkUnidades", function() {
		//div contenedor del checkbox seleccionado
		div_comp = $(this).parent().parent().parent();
		
		result = '#subtema_unidad'+$(this).val()+"_"+div_comp.attr("id");
		if( $(this).is(':checked') ) {
			if ($(result).text() == "") {
				$.ajax({
					url : "{{ path('subtemas_de_unidad') }}",
					type: "POST",
					data : { unidad: $(this).val(), componente: div_comp.attr("id") },
					success: function(html) {
						$(result).html(html);
					}
				});
			}else{
				$(result).show();
			}
		} else {
			$(result).hide();
		}
	});

	$("#generar").on( 'click', function() {
		asignarContenido();
	});

	function asignarContenido(){
		$("#contenido_evaluacion").val($("#filas_ejercicios").html());
		//alert("el contenido del div es:  " + $("#contenido_evaluacion").val());
		return false;
	}

			//evento que muestra el input de las cantidades, dependiendo si el subtema asociado ha sido chequeado
			$("#unidades").on('change', '.ch', function() {
				numberId = "cant"+$(this).attr("id");
				if( $(this).prop('checked') ) {
					$("#"+numberId).css("visibility", "visible");
					$("#"+numberId).val("1");
				}else{
					$("#"+numberId).val("0");
					$("#"+numberId).css("visibility", "hidden");
				}
			});

			$("#vista_previa").on("click", function(){

	        	//se obtienen las cantidades de ejercicios de los subtemas seleccionados
	        	var i=1;
	        	var myObj = {};
	        	$("input[name='cantidad[]']").each(function() {
	        		cant = $(this).val();
	        		if( cant != '0' && cant !='' ){
	        			var myObjChild=
		          		{
		          			"cantidad": cant,
		          		};
	        			myObj[i] = myObjChild;
	        			i++;
	        		}
	        	});
	        	if( i == 1 ){
	        		$("#aviso").show();
	        		$("#aviso").fadeOut(20000);
	        	}else

 				k=1;
		        $("input[name='subtema[]']:checked").each(function() {
		        	sub_sele = $(this).val();		        	
		            //comp: indica el componente al que pertenece el subtema
		            comp = $(this).parent().parent().parent().parent().parent().parent().attr("id");
		            
		          	myObj[k]["subtema"] = sub_sele;
		          	myObj[k]["componente"] = comp;

		            k++;
	        	});

	        	var myObj2 = {};
	        	j=1;
	        	$("#complejidades input[type='checkbox']:checked").each(function() {
		        	complejidad = $(this).val();
		        	myObj2[j] = complejidad;
		        	j++;
		        });
		        if( j == 1 ){
	        		$("#aviso2").show();
	        		$("#aviso2").fadeOut(20000);
	        	}        	

	        	if( i != 1 && j != 1 ){
	        		$("#info").show();
		        	$.ajax({
			            url : "{{ path('obt_ejercicios') }}",
			            type: "POST",
			            data: { "datos": JSON.stringify(myObj), "complejidades": JSON.stringify(myObj2) },
			            success: function(e) {
			                // la variable html representa toda la página junto con el select de subtema.
			                // el cual tomamos y colocamos para reemplazar el select actual.
			                $('#ejercicios').html(e);
			            }
			        });
			    }
			});

			//al elegir otra asignatura, se muestran las unidades del componente y asignatura seleccionados
			$("#{{ form.asignatura.vars.id }}").on( 'change', function() {
				asig= $("#{{ form.asignatura.vars.id }}").val();
				componente= $("#{{ form.tipo.vars.id }}").val();
				unidad_de_componente(asig, componente);
			});
			
			$("#{{ form.tipo.vars.id }}").on( 'change', function() {
				asig= $("#{{ form.asignatura.vars.id }}").val();
				componente = $(this).val();
				unidad_de_componente(asig, componente);
			});

			function unidad_de_componente(asig, componente){
				if( componente == "teorico" ){
					ajax(creatObjAjax("teorico", asig));
				}else{$("#{{ form.asignatura.vars.id }}").val()
					if( componente == "practico" ){
						ajax(creatObjAjax("practico", asig));
					}else{
						ajax(creatObjAjax("teorico_practico", asig));
					}
				}  
			}
	
	function ajax(calls){
		$.when($.ajax(calls[0].properties)).then(function(res){
			calls[0].callback(res);
			calls.splice(0, 1);
			if (calls.length) ajax(calls);
		});
	}

	function creatObjAjax(comp, asig){
		if(comp == "teorico" || comp == "practico"){
			var obj= [{properties: {
					url: "{{ path('unidad_de_componente') }}",
					type: "POST",
					data: {
						componente: comp,
						asignatura: asig
					}
				},
				callback: function(r){
					$('#'+comp).html(r);
					$('#'+comp).show();
					if(comp == "teorico"){
						$('#practico').hide();
					}else{
						$('#teorico').hide();
					}
				}
			}];
		}else{
			var obj= [{properties: {
						url: "{{ path('unidad_de_componente') }}",
						type: "POST",
						data: {
							componente: "teorico",
							asignatura: asig
						}
					},
					callback: function(r){
						$('#teorico').html(r);
						$('#teorico').show();
					}
				},
				{properties: {
						url: "{{ path('unidad_de_componente') }}",
						type: "POST",
						data: {
							componente: "practico",
							asignatura: asig
						}
					},
					callback: function(r){
						$('#practico').html(r);
						$('#practico').show();
						//Instrucciones
					}
				}usar jquery definido en un bundle simfony2
			];
		}
		return obj;
	}
});
