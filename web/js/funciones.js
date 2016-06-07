$(document).ready(function(){
    function confirmacion(){
        return confirm("¿Está seguro que desea eliminar el/los elemento(s) seleccionado(s)?");
    }
    $('#control').on("submit", "#myForm", function(e){
        if(confirmacion()){
            $('#control').off("submit", "#myForm");
            $("#myForm").submit();
        }else{
            e.preventDefault();
        }
        
    });
});
