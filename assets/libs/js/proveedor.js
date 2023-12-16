$(document).ready(function () {
    buscar_prov();
    var funcion;
    var edit=false;
    $(document).on('click', '.crearprov', function() {
        $('#form-crear-proveedor').trigger('reset');
        edit=false;
    });
    $('#form-crear-proveedor').submit(e => {
        e.preventDefault();
        let nombre = $('#nombre').val();
        let telefono = $('#telefono').val();
        let correo = $('#correo').val();
        let direccion = $('#direccion').val();
        let id_editado = $('#id_editar_prov').val();
        funcion = edit ? 'editar' : 'crear';
        $.post('../controller/ProveedorController.php', {id_editado, nombre, telefono, correo, direccion, funcion }, (response) => {
            if (response=='add') {
                $('#form-crear-proveedor').trigger('reset');
                Swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: 'Proveedor Creado Con Exito',
                  showConfirmButton: false,
                  timer: 1000
              })
              $('#newproveedor').modal('hide');
              buscar_prov();
        } else {
                $('#form-crear-proveedor').trigger('reset');
                Swal.fire({
                  position: 'center',
                  icon: 'error',
                  title: 'Ah Ocurrido Un Error!!',
                  showConfirmButton: false,
                  timer: 1000
              })
              $('#newproveedor').modal('hide');
              buscar_prov();
        }
      });
      e.preventDefault();
    });
    function buscar_prov(consulta) {
      $.post('../controller/ProveedorController.php', { consulta, funcion: 'buscar_prov' }, (response) => {
          try {
              const proveedors = JSON.parse(response);
              mostrarveedores(proveedors);
          } catch (error) {
              console.error("Error al analizar la respuesta JSON:", error);
          }
      });
  }
  function mostrarveedores(proveedors) {
      const provContainer = $('#proveedores');
      const template = proveedors.map(prov => `
          <div proId="${prov.id}" proNombre="${prov.nombre}" conCell="${prov.telefono}" addEmail="${prov.correo}" preDireccion="${prov.direccion}" proAvatar="${prov.avatar}" class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
              <div class="card bg-light">
                  <div class="card-header text-muted border-bottom-0">
                      <h1 class="badge badge-success">Proveerdor</h1>
                  </div>
                  <div class="card-body pt-0">
                      <div class="row">
                          <div class="col-7">
                              <h3 class="lead"><b>${prov.nombre}</b></h3>
                              <ul class="ml-4 mb-0 fa-ul text-muted">
                              <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone mr-1"></i></span>Telefono: ${prov.telefono}</li>
                                  <li class="small"><span class="fa-li"><i class="fas fa-lg fa-at mr-1"></i></span>Correo: ${prov.correo}</li>
                                  <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building mr-1"></i></span>Direccion: ${prov.direccion}</li>
                              </ul>
                          </div>
                          <div class="col-5 text-center">
                              <img src="${prov.avatar}" alt="" class="img-circle img-fluid">
                          </div>
                      </div>
                  </div>
                  <div class="card-footer">
                      <div class="text-right">
                          <button class="imagen btn btn-sm bg-teal" title="Editar Imagen" type="button" data-toggle="modal" data-target="#cambioavatar"><i class="fas fa-image"></i></button>
                          <button class="editar btn btn-sm btn-success" title="Editar Proveedor" type="button" data-toggle="modal" data-target="#newproveedor"><i class="fas fa-pencil-alt mr-1"></i></button>
                          <button class="borrar_prov btn btn-sm btn-danger" title="Eliminar Proveedor" type="button" data-toggle="modal" data-target="#remove-proveedors"><i class="fas fa-trash-alt mr-1"></i></button>
                      </div>
                  </div>
              </div>
          </div>
      `).join('');
      provContainer.empty().append(template);
  }
    $(document).on('keyup','#buscar_proveedor',function(){
        let valor = $(this).val();
        if (valor!==""){
        buscar_prov(valor);
      } else {
        buscar_prov();
      }
  });
  $(document).on('click','.imagen',(e)=>{
    funcion="cambiar_avatar";
    const elemento= $(this)[0].activeElement.parentElement.parentElement.parentElement.parentElement;
    const id=$(elemento).attr('proId');
    const avatar=$(elemento).attr('proAvatar');
    const nombre=$(elemento).attr('proNombre');
    $('#funcion').val(funcion);
    $('#id_logo_prod').val(id);
    $('#avatar').val(avatar);
    $('#logoactual1').attr('src',avatar);
    $('#nombre_logo').html(nombre);
  });
  $('#form-logo').submit((e) => {
    e.preventDefault();
    const fileInput = $('#foto')[0];
    const fileType = fileInput.files[0].type;
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/bmp'];
    if (!allowedTypes.includes(fileType)) {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'No se permite este archivo. Solo se permiten imágenes.',
            showConfirmButton: false,
            timer: 1500
        });
        return;
    }
    let formData = new FormData($('#form-logo')[0]);
    $.ajax({
        url: '../controller/ProveedorController.php',
        type: 'POST',
        data: formData,
        cache: false,
        processData: false,
        contentType: false
    }).done(function (response) {
        const json = JSON.parse(response);
        if (json.alert == 'edit') {
            $('#logoactual1').attr('src', json.ruta);
            buscar_prov();
            $('#form-logo').trigger('reset');
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Realizado Éxito',
                showConfirmButton: false,
                timer: 1500
            });
            $('#cambiarlogo').modal('hide');
        } else {
            $('#form-logo').trigger('reset');
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Ha Ocurrido Un Error!!',
                showConfirmButton: false,
                timer: 1500
            });
        }
        $('#cambiarlogo').modal('hide');
    }).fail(function (error) {
        console.error("Error en la solicitud AJAX:", error);
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Error en la solicitud AJAX',
            showConfirmButton: false,
            timer: 1500
        });
    });
});
  $(document).on('click', '.editar', (e) => {
    funcion='editar';
    const elemento = $(e.currentTarget).parent().parent().parent().parent();
    const id = $(elemento).attr('proId');
    const nombre=$(elemento).attr('proNombre');
    const telefono=$(elemento).attr('conCell');
    const correo=$(elemento).attr('addEmail');
    const direccion=$(elemento).attr('preDireccion');
    $('#id_editar_prov').val(id);
    $('#nombre').val(nombre);
    $('#telefono').val(telefono);
    $('#correo').val(correo);
    $('#direccion').val(direccion);
    edit=true;
  });
  $(document).on('click','.borrar_prov',(e)=>{
    funcion='borrar_prove';
    const elemento= $(this)[0].activeElement.parentElement.parentElement.parentElement.parentElement;
    const id=$(elemento).attr('proId');
    const nombre=$(elemento).attr('proNombre');
    const avatar=$(elemento).attr('proAvatar');
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger mr-1'
        },
        buttonsStyling: false
    });
    swalWithBootstrapButtons.fire({
        title: '¿Estás seguro de que deseas eliminar ' + nombre + '?',
        text: '¡No podrás revertir esto!',
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: '¡Sí, bórralo!',
        cancelButtonText: '¡No, cancela!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../controller/ProveedorController.php', { id, funcion, avatar }, (response) => {
                if (response === 'borrado') {
                    swalWithBootstrapButtons.fire(
                        'Eliminado',
                        'Tu Proveedor ' + nombre + ' ha sido eliminado.',
                        'success'
                    );
                    buscar_prov();
                } else {
                    swalWithBootstrapButtons.fire(
                        'Cancelado',
                        'Tu Proveedor ' + nombre + ' no fue eliminado porque está siendo usado en un Lote.',
                        'error'
                    );
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'Tu Proveedor ' + nombre + ' está a salvo :)',
                'error'
            );
        }
    });
    });
});
