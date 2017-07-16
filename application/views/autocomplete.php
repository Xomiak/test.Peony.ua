<script src="/js/autocomplete/jquery.ui.core.js"></script>
<script src="/js/autocomplete/jquery.ui.widget.js"></script>
<script src="/js/autocomplete/jquery.ui.position.js"></script>
<script src="/js/autocomplete/jquery.ui.autocomplete.js"></script>

<script>
  var j = jQuery.noConflict();
  j(function() {
    // function log( message ) {
    //   j( "<div/>" ).text( message ).prependTo( "#log" );
    //   j( "#log" ).scrollTop( 0 );
    // }
    j( "#birds" ).autocomplete({
      source: "/ajax/autocomplete/",
      minLength: 1,
      select: function( event, ui ) {
        // log( ui.item ?
        //   "Выбрано: " + ui.item.value + " aka " + ui.item.id :
        //   "Нет ничего подходящего " + this.value );
      }
    });
  });

//  j(document).ready(function() {
//    j("#kolvo").keydown(function(event) {
//      // Разрешаем нажатие клавиш backspace, del, tab и esc
//      if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
//          // Разрешаем выделение: Ctrl+A
//        (event.keyCode == 65 && event.ctrlKey === true) ||
//          // Разрешаем клавиши навигации: home, end, left, right
//        (event.keyCode >= 35 && event.keyCode <= 39)) {
//        return;
//      }
//      else {
//        // Запрещаем всё, кроме клавиш цифр на основной клавиатуре, а также Num-клавиатуре
//        if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
//          event.preventDefault();
//        }
//      }
//    });
//  });
</script>