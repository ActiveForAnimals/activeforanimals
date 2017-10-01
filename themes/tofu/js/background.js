/**
 * @file
 * Random background image selector.
 */

(function ($, Drupal) {
  var NUMBER_OF_BACKGROUND_IMAGES = 10;
  var PATH = drupalSettings.tofu.path;
  Drupal.behaviors.setBackground = {
    attach: function (context, settings) {
      var random_image = (Math.floor(Math.random() * NUMBER_OF_BACKGROUND_IMAGES) + 1) + '.jpg';
      $('.path-frontpage header').css({
        'background-image': 'url(' + PATH + '/images/splash/' + random_image + ')'
      });
    }
  }
})(jQuery, Drupal);
