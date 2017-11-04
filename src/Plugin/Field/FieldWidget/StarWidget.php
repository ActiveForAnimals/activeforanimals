<?php

namespace Drupal\activeforanimals\Plugin\Field\FieldWidget;

use Drupal;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the star widget.
 *
 * @FieldWidget(
 *   id = "star",
 *   module = "effective_activism",
 *   label = @Translation("Star widget"),
 *   field_types = {
 *     "integer"
 *   }
 * )
 */
class StarWidget extends WidgetBase implements WidgetInterface {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $rating = isset($items[$delta]->value) ? $items[$delta]->value : NULL;
    $settings = $items->first()->getDataDefinition()->getSettings();
    if (empty($settings['min']) || empty($settings['max'])) {
      return $element;
    }
    $i = $settings['min'];
    $element['#suffix'] = '<ul class="stars">';
    while ($i <= $settings['max']) {
      $element['#suffix'] .= sprintf('<li class="star" title="%s" data-value="%d"></li>', Drupal::translation()->formatPlural($i, 'Rate it 1 star', 'Rate it @stars stars', [
        '@stars' => $i,
      ]), $i);
      $i++;
    }
    $element['#suffix'] .= '</ul>';
    $element['#attached']['library'][] = 'activeforanimals/rate';
    $element['#attributes']['class'][] = 'star-rate';
    $element['#type'] = 'textfield';
    $element['#min'] = $settings['min'];
    $element['#max'] = $settings['max'];
    $element['#default_value'] = $rating;
    $element['#required'] = FALSE;
    $element['#element_validate'] = [[
      $this,
      'validateStars',
    ]];
    return ['value' => $element];
  }

  /**
   * Validate the address.
   */
  public function validateStars($element, FormStateInterface $form_state) {
    $value = $element['#value'];
    if (empty($value)) {
      $form_state->setError($element, t('Please select a rating.'));
    }
  }

}
