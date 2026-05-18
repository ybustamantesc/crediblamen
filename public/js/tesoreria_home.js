(function(){
  function openModal(url){
    fetch(url).then(function(r){return r.text();}).then(function(html){
      var container = document.getElementById('modalContainer');
      if(!container){ container = document.createElement('div'); container.id='modalContainer'; document.body.appendChild(container); }
      container.innerHTML = html;
    });
  }
  var m = document.getElementById('btnNuevoMovimiento');
  if(m){ m.addEventListener('click', function(){ openModal(base_url+'tesoreria/modal_movimiento'); }); }
  var p = document.getElementById('btnProgramarPago');
  if(p){ p.addEventListener('click', function(){ openModal(base_url+'tesoreria/modal_pago'); }); }
  var a = document.getElementById('btnArqueo');
  if(a){ a.addEventListener('click', function(){ openModal(base_url+'tesoreria/modal_arqueo'); }); }
})();
