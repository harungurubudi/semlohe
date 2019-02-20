(function ($, window, document, undefined) { 
  'use strict';

  var doRequest = function(params) {
    return $.ajax({
      url: params.url,
      data: params.data,
      type: 'PATCH',
      success: params.success,
      error: params.error
    });
  };

  $.fn.statusSwitcher = function (selector, options) {
    options = options === undefined ? {} : options;
    var init = function() {
      var success = options.success === undefined ? 
        function() {return true} : 
        options.success;
    
      var error = options.error === undefined ? 
        function() {return true} : 
        options.error;
      
      $(selector).change(function() {
        var status = $(this).is(':checked') ? 1 : 0;
        var id = $(this).data('id');
        var url = options.url.replace('(:id)', id) + '?value=' + status;
        NProgress.start();
        doRequest({
          url:url,
          data: {
            'value': status
          },
          success: function() {
            NProgress.done();
            $.notify({
              message: 'Status has been changed succesfully' 
            },{
              type: 'info'
            });
            success();
          },
          error: function() {
            NProgress.done();
            $.notify({
              message: 'Sorry, We got error while processiong your request' 
            },{
              type: 'error'
            });
            error();
          }
        });
      });
    };

    init();
  };
})(jQuery, window, document);

