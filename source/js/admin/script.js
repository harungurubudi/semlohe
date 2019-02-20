// Layout 
(function (layout) {
  layout(window.jQuery, window, document);
} (function ($, window, document) {
  $(function () {
    var topNavHeight = 115,
      elemBody = $('#site-body'),
      elemWindow = $(window);

    function recalibrateHeight() {
      var bodyHeight = elemWindow.height() - topNavHeight;
      elemBody.css('min-height', bodyHeight + 'px');
    }

    recalibrateHeight();
    elemWindow.resize(function() {
      recalibrateHeight();
    })

    // Datepicker init
    $('#datetimepicker').datetimepicker({
      format: 'YYYY-MM-DD HH:mm:ss'
    });

    // Confirmation dialog
    $('body').on('click', '.confirm', function(e) {
      e.preventDefault();
      var target = $(this).attr('href');
      bootbox.confirm("Are you sure? ", function(result){
        if (result) {
          window.location = target;
        }
      })
    });
  });
}));