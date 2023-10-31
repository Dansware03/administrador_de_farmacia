$(document).ready(function() {
    var funcion
    $('#form-crear-laboratorio').submit(e=>{
        let nombre_laboratory = $('#nombre-laboratorio').val();
        funcion='crear';
        $.post('../controller/LaboratoryController.php',{nombre_laboratory,funcion},(Response)=>{
            if (Response=='add') {
                $('#add-lab').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        $('#form-crear-laboratorio').trigger('reset');
                        });
                    });
                });
                } else {
                $('#noadd-lab').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        $('#form-crear-laboratorio').trigger('reset');
                        });
                    });
                });
                }
        });
        e.preventDefault();
    });
})