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
          // console.log(Response);
            const usuarios = JSON.parse(Response);
            let templete='';
            usuarios.forEach(usuario=> {
                templete+=`
                <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch" bis_skin_checked="1">
              <div class="card bg-light" bis_skin_checked="1">
                <div class="card-header text-muted border-bottom-0" bis_skin_checked="1">
                  <b>${usuario.tipo}</b>
                </div>
                <div class="card-body pt-0" bis_skin_checked="1">
                  <div class="row" bis_skin_checked="1">
                    <div class="col-7" bis_skin_checked="1">
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
                    <div class="col-5 text-center" bis_skin_checked="1">
                      <img src="${usuario.avatar}" alt="" class="img-circle img-fluid">
                    </div>
                  </div>
                </div>
                <div class="card-footer" bis_skin_checked="1">
                  <div class="text-right" bis_skin_checked="1">`;
                  if (tipo_usuario==3) {
                    if (usuario.tipo_usuario!=3) {
                      templete+=`
                      <button class="btn btn-danger mr-1">
                      <i class="fas fa-window-close mr-1"></i>Eliminar
                    </button>
                      `;
                    }
                    if (usuario.tipo_usuario==2) {
                      templete+=`
                      <button class="btn btn-primary ml-1">
                      <i class="fas fa-window-close mr-1"></i>Ascender
                    </button>
                    `;
                    }
                  } else {
                    if (tipo_usuario==1 && usuario.tipo_usuario!=1 && usuario.tipo_usuario!==3) {
                      templete+=`
                      <button class="btn btn-danger">
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
    $('#form-crear').submit(e=>{
      let nombre = $('#nombre').val();
      let apellido = $('#apellido').val();
      let edad = $('#edad').val();
      let ci = $('#ci').val();
      let genero = $('#genero').val();
      let pass = $('#pass').val();
      funcion='crear_usuario';
      $.post('../controller/UserController.php',{nombre,apellido,edad,ci,genero,pass,funcion},(Response)=>{
        if (Response=='add') {
          $('#add').hide('slow', function () {
            $(this).show(1000, function () {
            $(this).hide(2000, function () {
              $('#form-crear').trigger('reset');
              });
            });
          buscar_datos();
        });
        } else {
          $('#noadd').hide('slow', function () {
            $(this).show(1000, function () {
            $(this).hide(2000, function () {
                $('#form-crear').trigger('reset');
                });
            });
        });
        }
      });
      e.preventDefault();
    })
});