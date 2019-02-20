(function ($, window, document, undefined) { 
  'use strict';

  var doRequest = function(params) {
    return $.ajax({
      url: params.url,
      data: params.data,
      type: 'DELETE',
      success: params.success,
      error: params.error
    });
  };

  $.fn.deleteButton = function (selector, options) {
    options = options === undefined ? {} : options;    
    var init = function() {

      var success = options.success === undefined ? 
        function() {return true} : 
        options.success;
    
      var error = options.error === undefined ? 
        function() {return true} : 
        options.error;
      
      $(selector).click(function(e) {
        e.preventDefault();
        
        var id = $(this).data('id');
        var url = options.url.replace('(:id)', id);
        var callback = function() {
          NProgress.start();
          doRequest({
            url:url,
            data: {
              'value': status
            },
            success: function() {
              NProgress.done();
              $('#row-' + id).remove();
              $.notify({
                message: 'Data has been deleted succesfully' 
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
        };

        bootbox.confirm("Are you sure? ", function(result) {
          if (result) {
            callback();
          }
        })

      });
    };

    init();
  };

})(jQuery, window, document);
  
