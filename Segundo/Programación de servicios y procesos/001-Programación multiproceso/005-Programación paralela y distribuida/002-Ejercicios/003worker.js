self.addEventListener('message', function(e) {
  console.log('Mensaje recibido del script principal');
 
  var data = '¡Hola script principal!';
  //postMessage(data);
  self.postMessage(data);
});

