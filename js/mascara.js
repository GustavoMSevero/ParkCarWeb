function inputHandler(masks, max, event) {
    var c = event.target;
    var v = c.value.replace(/\D/g, '');
    var m = c.value.length > max ? 1 : 0;
    VMasker(c).unMask();
    VMasker(c).maskPattern(masks[m]);
    c.value = VMasker.toPattern(v, masks[m]);
  }
  
  var telMask = ['(99) 9999-99999', '(99) 99999-9999'];
  var tel = document.querySelector('#tel');
  VMasker(tel).maskPattern(telMask[0]);
  tel.addEventListener('input', inputHandler.bind(undefined, telMask, 14), false);
  
  var docMask = ['999.999.999-999', '99.999.999/9999-99'];
  var doc = document.querySelector('#doc');
  VMasker(doc).maskPattern(docMask[0]);
  doc.addEventListener('input', inputHandler.bind(undefined, docMask, 14), false);

  var valorMask = ['1.000,00'];
  var doc = document.querySelector('#doc');
  VMasker(doc).maskPattern(valorMask[0]);
  doc.addEventListener('input', inputHandler.bind(undefined, valorMask, 14), false);