jQuery(function() {
  jQuery('body')
    .prepend('<div style="opacity: 0.75; position: fixed; top: 0; left: 5%; right: 5%; font-size: 110%; border: 1px solid #aaa; border-top: 0; background-color: #eee; text-align: center; margin: 0 auto;"><a style="text-decoration: none; color: #800; font-weight: bold; padding: 6px; display: block;" href="#" onclick="window.close(); return false;">Cerrar esta ventana y volver al editor</a></div>');

  alert('Esta es una previsualización de la distribución que está editando.\nRecuerde que sus cambios aún no han sido guardados.\nPara guardarlos puede cerrar esta página y volver al editor de distribuciones.');
});