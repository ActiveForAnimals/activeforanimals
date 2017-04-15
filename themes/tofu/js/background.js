/**
 * @file
 * Random background image selector.
 */

(function ($, Drupal) {
  var NUMBER_OF_BACKGROUND_IMAGES = 8;
  var PATH = drupalSettings.tofu.path;
  Drupal.behaviors.setBackground = {
    attach: function (context, settings) {
      var random_image = (Math.floor(Math.random() * NUMBER_OF_BACKGROUND_IMAGES) + 1) + '.jpg';
      $('.layout-container').css({
        'background-image': 'url(' + PATH + '/images/backgrounds/' + random_image + ')',
        'background-size': 'cover',
      });
    }
  }
})(jQuery, Drupal);
