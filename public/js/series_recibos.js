/* Frontend JS para Series de Recibos
   Requiere jQuery y Bootstrap JS
   Endpoints esperados (crear un controlador 'Series_recibos'):
     GET  base_url+'series_recibos/list'       -> devuelve JSON array de series
     GET  base_url+'series_recibos/get/{id}'   -> devuelve JSON objeto serie
     POST base_url+'series_recibos/save'       -> guarda nueva o actualiza (form data)
*/
;(function($){
    $(document).ready(function(){
        var base = window.base_url || ('/' );

        function reloadTable(){
            // simple reload via AJAX and rebuild tbody
            $.get(base + 'series_recibos/list', function(res){
                if (!res || !Array.isArray(res)) return;
                var tbody = $('#series-table tbody');
                tbody.empty();
                res.forEach(function(s){
                    var row = '<tr>' +
                        '<td>'+s.idserie+'</td>' +
                        '<td>'+htmlEscape(s.codigo)+'</td>' +
                        '<td class="serie-nombre">'+htmlEscape(s.nombre)+'</td>' +
                        '<td>'+ (s.consecutivo==null?0:s.consecutivo) +'</td>' +
                        '<td>'+ (s.ultimo_emitido==null? '': s.ultimo_emitido) +'</td>' +
                        '<td>'+ (s.estado==1? '<span class="badge badge-success">ACTIVO</span>':'<span class="badge badge-warning">INACTIVO</span>') +'</td>' +
                        '<td>'+
                          '<button class="btn btn-sm btn-info btn-view" data-id="'+s.idserie+'">Ver</button> ' +
                          '<button class="btn btn-sm btn-warning btn-edit" data-id="'+s.idserie+'">Editar</button>' +
                        '</td>' +
                        '</tr>';
                    tbody.append(row);
                });
            }, 'json');
        }

        function htmlEscape(str){
            if (str === null || str === undefined) return '';
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        // abrir modal nueva serie
        $('#btnNuevaSerie').on('click', function(){
            $('#formSerie')[0].reset();
            $('#idserie').val('');
            $('#modalSerieTitle').text('Nueva Serie');
            $('#modalSerie').modal('show');
        });

        // delegado para editar / ver
        $('#series-table').on('click', '.btn-edit, .btn-view', function(){
            var id = $(this).data('id');
            var isView = $(this).hasClass('btn-view');
            $.get(base + 'series_recibos/get/' + id, function(res){
                if (!res) return alert('Serie no encontrada');
                $('#idserie').val(res.idserie);
                $('#codigo').val(res.codigo);
                $('#nombre').val(res.nombre);
                $('#consecutivo').val(res.consecutivo);
                $('#estado').val(res.estado?1:0);
                $('#modalSerieTitle').text(isView? ('Ver Serie ' + res.codigo) : ('Editar Serie ' + res.codigo));
                // if view, disable inputs
                $('#formSerie input, #formSerie select').prop('disabled', isView);
                $('#btnSaveSerie').toggle(!isView);
                $('#modalSerie').modal('show');
            }, 'json');
        });

        // guardar serie
        $('#btnSaveSerie').on('click', function(){
            var data = $('#formSerie').serialize();
            $.post(base + 'series_recibos/save', data, function(res){
                if (!res) return alert('Error en respuesta');
                if (res.success){
                    $('#modalSerie').modal('hide');
                    reloadTable();
                } else {
                    alert(res.message || 'Error al guardar');
                }
            }, 'json');
        });

        // Inicializa
        reloadTable();
    });
})(jQuery);
