$(document).ready(function() {
  var funcion;
  var tipo_usuario = $('#tipo_usuario').val();
  if (tipo_usuario==2) {
    $('#button_crear').hide();
  }
    buscar_datos();
    function buscar_datos(consulta) {
        funcion='buscar_usuario_adm';
        $.post('../controller/UserController.php',{consulta,funcion},(Response)=>{
            const usuarios = JSON.parse(Response);
            let templete='';
            usuarios.forEach(usuario=> {
                templete+=`
                <div usuarioId="${usuario.id}" class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch" >
              <div class="card bg-light" >
                <div class="card-header text-muted border-bottom-0" >`;
                if (usuario.tipo_usuario==3) {
                  templete+=`<h1 class="badge badge-danger">${usuario.tipo}</h1>`;
                }
                if (usuario.tipo_usuario==1) {
                  templete+=`<h1 class="badge badge-primary">${usuario.tipo}</h1>`;
                }
                if (usuario.tipo_usuario==2) {
                  templete+=`<h1 class="badge badge-success">${usuario.tipo}</h1>`;
                }
                templete+=`</div>
                <div class="card-body pt-0" >
                  <div class="row" >
                    <div class="col-7" >
                      <h2 class="lead"><b>${usuario.nombre} ${usuario.apellidos}</b></h2>
                      <p class="text-muted text-sm"><b>Información: </b> ${usuario.info}</p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                      <li class="small"><span class="fa-li"><i class="fas fa-lg fa-id-card"></i></span> C.i: ${usuario.ci}</li>
                      <li class="small"><span class="fa-li"><i class="fas fa-lg fa-birthday-cake"></i></span> Edad: ${usuario.edad}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Telefono: ${usuario.telefono}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-at"></i></span> Email: ${usuario.correo}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-smile-wink"></i></span> Genero: ${usuario.genero}</li>
                      </ul>
                    </div>
                    <div class="col-5 text-center" >
                      <img src="${usuario.avatar}" alt="" class="img-circle img-fluid">
                    </div>
                  </div>
                </div>
                <div class="card-footer" >
                  <div class="text-right" >`;
                  if (tipo_usuario==3) {
                    if (usuario.tipo_usuario!=3) {
                      templete+=`
                      <button class="delete-user btn btn-danger mr-1" type="button" data-toggle="modal" data-target="#check">
                      <i class="fas fa-window-close mr-1"></i>Eliminar
                    </button>
                      `;
                    }
                    if (usuario.tipo_usuario==2) {
                      templete+=`
                      <button class="ascender btn btn-primary ml-1" type="button" data-toggle="modal" data-target="#check">
                      <i class="fas fa-sort-amount-up mr-1"></i>Ascender
                    </button>
                    `;
                    } if (usuario.tipo_usuario==1) {
                      templete+=`
                      <button class="descender btn btn-secondary ml-1" type="button" data-toggle="modal" data-target="#check">
                      <i class="fas fa-sort-amount-down mr-1"></i>Descender
                    </button>
                    `;
                    }
                  } else {
                    if (tipo_usuario==1 && usuario.tipo_usuario!=1 && usuario.tipo_usuario!==3) {
                      templete+=`
                      <button class="delete-user btn btn-danger" type="button" data-toggle="modal" data-target="#check">
                      <i class="fas fa-window-close mr-1"></i>Eliminar
                    </button>
                      `;
                    }
                  }
                    templete+=`
                  </div>
                </div>
              </div>
            </div>
                `;
            })
            $('#usuarios').html(templete);
          });
    }
    $(document).on('keyup','#buscar',function(){
        let valor = $(this).val();
        if (valor!==""){

            buscar_datos(valor);

        } else {

          buscar_datos();
        }
    });
    $('#form-crear').submit(function (e) {
      e.preventDefault();
      const formData = {
        nombre: $('#nombre').val(),
        apellido: $('#apellido').val(),
        edad: $('#edad').val(),
        ci: $('#ci').val(),
        genero: $('#genero').val(),
        pass: $('#pass').val(),
        funcion: 'crear_usuario'
      };
      $.post('../controller/UserController.php', formData, function (response) {
        if (response === 'add') {
          showSuccessMessage();
          $('#form-crear').trigger('reset');
          buscar_datos();
        } else {
          showErrorMessage();
        }
      });
    });
    function showSuccessMessage() {
      $('#add').hide('slow', function () {
        $(this).show(1000, function () {
          $(this).hide(2000, function () {
            $('#form-crear').trigger('reset');
          });
        });
      });
    }
    function showErrorMessage() {
      $('#noadd').hide('slow', function () {
        $(this).show(1000, function () {
          $(this).hide(2000, function () {
            $('#form-crear').trigger('reset');
          });
        });
      });
    }
    $(document).on('click','.ascender',(e)=>{
      const elemento= $(this)[0].activeElement.parentElement.parentElement.parentElement.parentElement;
      const id=$(elemento).attr('usuarioId');
      funcion='ascender';
      $('#id_user_rol').val(id);
      $('#funcion').val(funcion);
    });
    $(document).on('click','.descender',(e)=>{
      const elemento= $(this)[0].activeElement.parentElement.parentElement.parentElement.parentElement;
      const id=$(elemento).attr('usuarioId');
      funcion='descender';
      $('#id_user_rol').val(id);
      $('#funcion').val(funcion);
    });
    $(document).on('click','.delete-user',(e)=>{
      const elemento= $(this)[0].activeElement.parentElement.parentElement.parentElement.parentElement;
      const id=$(elemento).attr('usuarioId');
      funcion='delete_user';
      $('#id_user_rol').val(id);
      $('#funcion').val(funcion);
    });
    $('#form-check').submit(e=>{
      let pass=$('#oldpass').val();
      let id_usuario=$('#id_user_rol').val();
      funcion=$('#funcion').val();
      $.post('../controller/UserController.php',{pass,id_usuario,funcion},(response)=>{
        if (response=='up' || response=='donw' || response=='delete') {
          $('#yes-rol').hide('slow', function () {
            $(this).show(1000, function () {
            $(this).hide(2000, function () {
                $('#form-check').trigger('reset');
                });
            });
        });
        } else {
          $('#no-rol').hide('slow', function () {
            $(this).show(1000, function () {
            $(this).hide(2000, function () {
                $('#form-check').trigger('reset');
                });
            });
        });
        }
      buscar_datos();
      });
      e.preventDefault();
    });
});