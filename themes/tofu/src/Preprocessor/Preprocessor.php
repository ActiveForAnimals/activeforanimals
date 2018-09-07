<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Url;

/**
 * Preprocessor abstract class.
 */
abstract class Preprocessor implements PreprocessorInterface {

  const ELEMENT_CLASS_FORMAT = 'element-%s';
  const LOGO_200X200 = 'logo_200x200';
  const LOGO_110X110 = 'logo_110x110';
  const LOGO_50X50 = 'logo_50x50';
  const GOOGLE_MAP_PARAMETER_TEMPLATE = '%s?q=%s&zoom=%d&key=%s';
  const GOOGLE_MAP_BASE_URL = 'https://www.google.com/maps/embed/v1/place';
  const GOOGLE_MAP_ZOOM_LEVEL = 15;

  /**
   * EntityTypeManager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * The variables to preprocess.
   *
   * @var array
   */
  protected $variables;

  /**
   * Constructor.
   */
  public function __construct(array $variables) {
    $this->variables = $variables;
    $this->entityTypeManager = Drupal::entityTypeManager();
  }

  /**
   * Returns a container element.
   *
   * @param array $classes
   *   The classes to include.
   *
   * @return array
   *   The container render element array.
   */
  protected function getContainer(array $classes) {
    $container = [
      '#type' => 'container',
      '#attributes' => [
        'class' => array_merge(['element'], $classes),
      ],
    ];
    return $container;
  }

  /**
   * {@inheritdoc}
   */
  abstract public function preprocess();

  /**
   * Returns a render array for an element.
   *
   * @param string $text
   *   The button text.
   * @param string $element_name
   *   The element name.
   * @param \Drupal\Core\Url $url
   *   The url to link to.
   *
   * @return array
   *   A render array.
   */
  protected function wrapButton($text, $element_name, Url $url) {
    $content = $this->getContainer([
      'view',
      'button',
      sprintf(self::ELEMENT_CLASS_FORMAT, $element_name),
    ]);
    if (empty($url)) {
      $content['element'] = $text;
    }
    else {
      $content['element'] = [
        '#type' => 'markup',
        '#markup' => Drupal::l(
          $text,
          $url
        ),
      ];
    }
    return $content;
  }

  /**
   * Returns a render array for an element.
   *
   * @param string $text
   *   The element text.
   * @param string $element_name
   *   The element name.
   * @param \Drupal\Core\Url $url
   *   A url to link to.
   *
   * @return array
   *   A render array.
   */
  protected function wrapElement($text, $element_name, Url $url = NULL) {
    $content = $this->getContainer([
      'view',
      sprintf(self::ELEMENT_CLASS_FORMAT, $element_name),
    ]);
    if (empty($url)) {
      $content['element'] = [
        '#type' => 'markup',
        '#markup' => $text,
      ];
    }
    else {
      $content['element'] = [
        '#type' => 'markup',
        '#markup' => Drupal::l(
          $text,
          $url
        ),
      ];
    }
    return $content;
  }

  /**
   * Returns a render array for an entity field.
   *
   * @param \Drupal\Core\Field\FieldItemList $field
   *   The field to process.
   * @param \Drupal\Core\Url $url
   *   An optional url to link the element to.
   *
   * @return array
   *   A render array.
   */
  protected function wrapField(FieldItemList $field, Url $url = NULL) {
    $content = $this->getContainer([
      'field',
      'view',
      sprintf(self::ELEMENT_CLASS_FORMAT, $field->getName()),
    ]);
    $display_options = [
      'label' => 'hidden',
    ];
    // Add date format if field is a datefield.
    if ($field->getItemDefinition()->getDataType() === 'field_item:datetime') {
      $display_options['settings']['format_type'] = 'iso_8601_weekday';
    }
    if (empty($url)) {
      $display_options['settings']['link'] = FALSE;
      $content['element'] = $field->view($display_options);
    }
    else {
      $content['element'] = [
        '#type' => 'markup',
        '#markup' => Drupal::l(
          $field->view($display_options),
          $url
        ),
      ];
    }
    // Remove superfluous part of url text for link field and set target.
    if ($field->getItemDefinition()->getDataType() === 'field_item:link') {
      $content['element'][0]['#title'] = preg_replace('/https?:\/\/(?:www\.)?/', '', $content['element'][0]['#title']);
      if (strlen($content['element'][0]['#title']) > 40) {
        $content['element'][0]['#title'] = substr($content['element'][0]['#title'], 0, 33) . '...';
      }
      $content['element'][0]['#attributes']['target'] = '_blank';
    }
    return $content;
  }

  /**
   * Returns a render array for a form element.
   *
   * @param array $form_element
   *   The form element to process.
   * @param string $form_element_name
   *   The form element name.
   *
   * @return array
   *   A render array.
   */
  protected function wrapFormElement(array $form_element, $form_element_name) {
    $content = $this->getContainer([
      'form',
      sprintf(self::ELEMENT_CLASS_FORMAT, $form_element_name),
    ]);
    $content['element'][$form_element_name] = $form_element;
    return $content;
  }

  /**
   * Returns a render array for a form element.
   *
   * @param array $render_element
   *   The form element to process.
   * @param string $render_element_name
   *   The form element name.
   *
   * @return array
   *   A render array.
   */
  protected function wrapRenderElement(array $render_element, $render_element_name) {
    $content = $this->getContainer([
      'view',
      sprintf(self::ELEMENT_CLASS_FORMAT, $render_element_name),
    ]);
    $content['element'][$render_element_name] = $render_element;
    return $content;
  }

  /**
   * Returns a render array for an image.
   *
   * @param string $uri
   *   The field to process.
   * @param string $element_name
   *   The name of the element.
   * @param string $image_style
   *   The image style to display the image with.
   * @param \Drupal\Core\Url $url
   *   A url to link to.
   *
   * @return array
   *   A render array.
   */
  protected function wrapImage($uri, $element_name, $image_style = NULL, Url $url = NULL) {
    $content = $this->getContainer([
      'image',
      'view',
      sprintf(self::ELEMENT_CLASS_FORMAT, $element_name),
    ]);
    if (UrlHelper::isExternal($uri)) {
      $element = [
        '#theme' => 'image',
        '#uri' => $uri,
      ];
      if (!empty($url)) {
        $content['element'] = [
          '#type' => 'markup',
          '#markup' => Drupal::l(
            $element,
            $url
          ),
        ];
      }
      else {
        $content['element'] = $element;
      }
    }
    else {
      $image = \Drupal::service('image.factory')->get($uri);
      if (!$image->isValid()) {
        return [];
      }
      if (!empty($image_style)) {
        $element = [
          '#theme' => 'image_style',
          '#width' => $image->getWidth(),
          '#height' => $image->getHeight(),
          '#uri' => $uri,
          '#style_name' => $image_style,
        ];
      }
      else {
        $element = [
          '#theme' => 'image',
          '#width' => $image->getWidth(),
          '#height' => $image->getHeight(),
          '#uri' => $uri,
        ];
      }
      if (!empty($url)) {
        $content['element'] = [
          '#type' => 'markup',
          '#markup' => Drupal::l(
            $element,
            $url
          ),
        ];
      }
      else {
        $content['element'] = $element;
      }
    }
    return $content;
  }

  /**
   * Returns a render array for an image.
   *
   * @param \Drupal\Core\Url $url
   *   A url to link to.
   * @param string $element_name
   *   The name of the element.
   *
   * @return array
   *   A render array.
   */
  protected function wrapIframe(Url $url, $element_name) {
    $content = $this->getContainer([
      'image',
      'view',
      sprintf(self::ELEMENT_CLASS_FORMAT, $element_name),
    ]);
    $content['element'] = [
      '#type' => 'markup',
      '#markup' => sprintf('<iframe frameborder="0" src="%s" allowfullscreen></iframe>', $url->toString()),
      '#allowed_tags' => ['iframe', 'html'],
    ];
    return $content;
  }

  /**
   * Returns a Google map image from a location.
   *
   * @param array $locations
   *   The locations to render as a map.
   *
   * @return \Drupal\Core\Url
   *   A map url.
   */
  protected function getMap(array $locations) {
    $location = array_pop($locations);
    return Url::fromUri(sprintf(
      self::GOOGLE_MAP_PARAMETER_TEMPLATE,
      self::GOOGLE_MAP_BASE_URL,
      $location['address'],
      self::GOOGLE_MAP_ZOOM_LEVEL,
      Drupal::config('effective_activism.settings')->get('google_maps_embed_api_key')
    ));
  }

}
