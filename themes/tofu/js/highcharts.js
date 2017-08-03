/**
 * @file
 * Charting glue.
 */

(function ($, Drupal) {
  Drupal.behaviors.highcharts = {
    attach: function (context, settings) {
      Highcharts.chart('chart', drupalSettings.highcharts);
    }
  }
})(jQuery, Drupal);
