
$(document).ready(function() {

  $(document).on('click', '[data-toggle-listing]', function(e) {
    e.preventDefault();
    var $target = $(e.currentTarget);

    if($target.hasClass('active')) {
      return;
    }

    var display = $target.data('display-type');
    var $btns = $('[data-toggle-listing]');
    $btns.removeClass('active');
    $target.addClass('active');

    $.ajax({
      url: listDisplayAjaxUrl,
      type: 'POST',
      dataType: 'json',
      data: {
        displayType: display
      },
      success: function() {
        prestashop.emit('updateFacets', window.location.href);
      }
    })
    .fail(function(err) {
      error(err);
    });
  })
})

