<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\effective_activism\Entity\DataType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;

/**
 * Creates test data.
 */
class CreateData {

  const TITLE = 'Test data type';
  const ID = 'data_type_test';
  const FIELD_LABEL = 'Integer input';
  const BUNDLE = 'data_type_test';
  const ENTITY_TYPE_ID = 'data';
  const FIELD_NAME = 'field_integer_input';
  const FIELD_TYPE = 'integer';
  const FIELD_CARDINALITY = 1;
  const FIELD_SETTINGS = [
    'min' => '0',
  ];

  /**
   * Constructor.
   */
  public function __construct() {
  }

  /**
   * Create data.
   */
  public function execute() {
    // Create data type.
    $data_type = DataType::create([
      'id' => self::ID,
      'label' => self::TITLE,
    ]);
    $data_type->save();
    // Check if field exists and create as necessary.
    $field_storage = FieldStorageConfig::loadByName(self::ENTITY_TYPE_ID, self::FIELD_NAME);
    if (empty($field_storage)) {
      $field_storage = FieldStorageConfig::create([
        'field_name' => self::FIELD_NAME,
        'entity_type' => self::ENTITY_TYPE_ID,
        'type' => self::FIELD_TYPE,
        'cardinality' => self::FIELD_CARDINALITY,
        'module' => 'core',
        'settings' => self::FIELD_SETTINGS,
      ])->save();
    }
    $field = FieldConfig::loadByName(self::ENTITY_TYPE_ID, self::BUNDLE, self::FIELD_NAME);
    if (empty($field)) {
      $field = FieldConfig::create([
        'field_name' => self::FIELD_NAME,
        'entity_type' => self::ENTITY_TYPE_ID,
        'bundle' => self::BUNDLE,
        'label' => self::FIELD_LABEL,
      ]);
      $field
        ->setRequired(TRUE)
        ->save();
    }
    // Form display settings for field_integer_input.
    entity_get_form_display(self::ENTITY_TYPE_ID, self::BUNDLE, 'default')
      ->setComponent('field_integer_input', [
        'type' => 'number',
      ])
      ->save();
    // View display settings for field_integer_input.
    entity_get_display(self::ENTITY_TYPE_ID, self::BUNDLE, 'default')
      ->setComponent('field_integer_input', [
        'type' => 'number_integer',
      ])
      ->save();
    return $data_type;
  }

}
