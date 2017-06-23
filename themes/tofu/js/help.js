/**
 * @file
 * Displays a tour modal.
 */

(function ($) {
  Drupal.behaviors.someArbitraryKey = {
    attach: function(context) {
      $('#activeforanimals_help').click(function(event) {
        var model = new Drupal.tour.models.StateModel();
        new Drupal.tour.views.ToggleTourView({
          el: $(context).find('#toolbar-tab-tour'),
          model: model
        });
        model
          // Allow other scripts to respond to tour events.
          .on('change:isActive', function (model, isActive) {
            $(document).trigger((isActive) ? 'drupalTourStarted' : 'drupalTourStopped');
          })
          // Initialization: check whether a tour is available on the current
          // page.
          .set('tour', $(context).find('ol#tour'))
          // Start the tour immediately if toggled via query string.
          .set('isActive', true);
        event.preventDefault();
        event.stopPropagation();
      });
    }
  }
}(jQuery));
