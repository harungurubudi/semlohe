(function ($, window, document) { 
  'use strict';
  // Chk control function here
  $.fn.chkControl = function (selector, options) {
    options = options === undefined ? {} : options;
    var hash = Math.random().toString(36).substr(2, 9);

    /**
     * Initialize parent dom
     */
    var dom = $('<ul class="dataform__chk-control" id="chk-control-' + hash  + '">' +
      ' <li>' +
      '   <a ' +
      '     data-action="check-all"' +
      '     href="#"' +
      '   >Check All</a>' +
      ' </li>' +
      ' <li>' +
      '   <a ' +
      '     data-action="uncheck-all"' +
      '     href="#"' +
      '   >Uncheck All</a>' +
      ' </li>' +
      '</ul>');

    $(selector).append(dom);

    $(selector).on('click', 'a', function(e) {
      e.preventDefault();
      var action = $(this).attr('data-action');
      if (action === 'check-all') {
        $(options.targetSelector).prop('checked', true);
      } else {
        $(options.targetSelector).prop('checked', false);
      }
    });    
  
  }
})(jQuery, window, document);


  