<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
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

  /**
   * EntityTypeManager.
   *
   * @var  \Drupal\Core\Entity\EntityTypeManager;
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
    if (empty($url)) {
      $content['element'] = $field->view([
        'label' => 'hidden',
        'settings' => [
          'link' => FALSE
        ],
      ]);
    }
    else {
      $content['element'] = [
        '#type' => 'markup',
        '#markup' => Drupal::l(
          $field->view('full'),
          $url
        ),
      ];
    }
    return $content;
  }

  /**
   * Returns a render array for a form element.
   *
   * @param array $field
   *   The form element to process.
   * @param string $field_name
   *   The field name.
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

}
