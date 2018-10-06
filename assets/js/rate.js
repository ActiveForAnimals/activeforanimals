/**
 * @file
 * Rate functionality for star widget.
 */

(function ($, Drupal) {
  $('.stars .star').on('mouseover', function() {
    var currentStar = parseInt($(this).data('value'), 10);
    $('.stars').children('.star').each(function(e) {
      if (e < currentStar) {
        $(this).addClass('hover');
      }
      else {
        $(this).removeClass('hover');
      }
    });
  }).on('mouseout', function() {
  $('.stars').children('.star').each(
    function(e) {
      $(this).removeClass('hover');
    });
  }).on('click', function() {
    var currentStar = parseInt($(this).data('value'), 10);
    var stars = $('.stars').children('.star');
    for (i = 0; i < stars.length; i++) {
      $(stars[i]).removeClass('selected');
    }
    for (i = 0; i < currentStar; i++) {
      $(stars[i]).addClass('selected');
    }
    $('.star-rate').val(parseInt($('.stars li.selected').last().data('value'), 10));
  });
})(jQuery, Drupal);
