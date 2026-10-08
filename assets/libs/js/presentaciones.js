/**
 * SIMAP - Gestión de Presentaciones Farmacéuticas y Formas de Empaque
 *
 * Administra el catálogo de empaques y presentaciones (Ampolla, Caja, Frasco, Blíster, etc.).
 * Implementa el flujo asíncrono para registro, búsqueda interactiva, modificación modal
 * y borrado protegido por verificación de productos vinculados.
 *
 * Flujo de Integración Extremo a Extremo:
 * 1. UI: Formulario modal `#form-crear-presentacion` en `adm_presentacion.php`.
 * 2. AJAX: POST hacia `assets/controller/PresentacionesController.php`.
 * 3. Backend: Métodos en modelo `Presentacion` (`crear`, `buscar`, `editar`, `borrar_pre`).
 * 4. Respuesta: Alertas modales y actualización de filas en el contenedor `#presentaciones`.
 *
 * @package SIMAP\Frontend\JS
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

$(document).ready(function() {
  buscar_pre();
  var edit = false;

  // Registrar o actualizar una presentación
  $('#form-crear-presentacion').submit(function (e) {
      e.preventDefault();
      let nombre_pre = $('#nombre-presentacion').val();
      let id_editado = $('#id_editar_presentacion').val();
      let funcion = id_editado ? 'editar' : 'crear';

      $.post('../controller/PresentacionesController.php', { nombre_pre, id_editado, funcion })
        .done(function (Response) {
          $('#form-crear-presentacion').trigger('reset');
          buscar_pre();

          if (Response === 'add' || Response === 'edit') {
            Swal.fire({
              position: 'center',
              icon: 'success',
              title: Response === 'add' ? 'Presentación Creada con Éxito' : 'Presentación Actualizada',
              showConfirmButton: false,
              timer: 1000
            }).then(() => {
              const modalObj = bootstrap.Modal.getInstance(document.getElementById('crear-presentacion'));
              if (modalObj) modalObj.hide();
            });
          } else {
            Swal.fire({
              position: 'center',
              icon: 'error',
              title: 'No se pudo guardar la presentación',
              showConfirmButton: false,
              timer: 1500
            });
          }
        })
        .always(function () {
          edit = false;
          $('#id_editar_presentacion').val('');
        });
  });

  // Consultar presentaciones y construir filas de tabla
  function buscar_pre(consulta) {
      let funcion = "buscar";
      $.post('../controller/PresentacionesController.php', { consulta, funcion }, (Response) => {
          const presentaciones = JSON.parse(Response);
          const presentacionContainer = $('#presentaciones');
          if (!presentaciones || presentaciones.length === 0) {
              presentacionContainer.html(`<tr><td colspan="2" class="text-center py-4 text-muted">No se encontraron presentaciones.</td></tr>`);
              return;
          }

          const template = presentaciones.map(presentacion => `
              <tr preId="${presentacion.id}" preNombre="${presentacion.nombre}">
                  <td class="ps-3 fw-semibold text-dark">
                      <i class="bi bi-box-seam text-primary me-2"></i>${presentacion.nombre}
                  </td>
                  <td class="text-end pe-3">
                      <button class="editar_pre btn btn-sm btn-outline-success me-1" title="Editar" type="button" data-bs-toggle="modal" data-bs-target="#crear-presentacion">
                          <i class="bi bi-pencil"></i>
                      </button>
                      <button class="borrar_pre btn btn-sm btn-outline-danger" title="Eliminar" type="button">
                          <i class="bi bi-trash"></i>
                      </button>
                  </td>
              </tr>
          `).join('');
          presentacionContainer.html(template);
      });
  }

  // Filtrado al teclear en el campo de búsqueda
  $(document).on('keyup', '#buscar-presentacion', function () {
      let valor = $(this).val();
      buscar_pre(valor !== "" ? valor : undefined);
  });

  // Eliminación con confirmación y control de dependencias
  $(document).on('click', '.borrar_pre', function () {
      const funcion = "borrar";
      const elemento = $(this).closest('tr');
      const id = elemento.attr('preId');
      const nombre = elemento.attr('preNombre');

      Swal.fire({
          title: `¿Eliminar Presentación "${nombre}"?`,
          text: "Esta acción no se puede deshacer.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#64748b',
          confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
          cancelButtonText: 'Cancelar'
      }).then((result) => {
          if (result.isConfirmed) {
              $.post('../controller/PresentacionesController.php', { id, funcion }, (response) => {
                  if (response === 'borrado') {
                      Swal.fire({
                          position: 'center',
                          icon: 'success',
                          title: 'Presentación Eliminada',
                          showConfirmButton: false,
                          timer: 1000
                      });
                      buscar_pre();
                  } else {
                      Swal.fire({
                          position: 'center',
                          icon: 'error',
                          title: 'No se puede eliminar (insumos asociados)',
                          showConfirmButton: false,
                          timer: 1500
                      });
                  }
              });
          }
      });
  });

  // Preparar formulario para edición de presentación
  $(document).on('click', '.editar_pre', function () {
      const elemento = $(this).closest('tr');
      const id = elemento.attr('preId');
      const nombre = elemento.attr('preNombre');
      $('#nombre-presentacion').val(nombre);
      $('#id_editar_presentacion').val(id);
      $('#crearPresLabel').html('<i class="bi bi-pencil-square me-2"></i>Editar Presentación');
      edit = true;
  });
});
