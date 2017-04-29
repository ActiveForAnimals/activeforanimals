<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\effective_activism\Entity\Data;
use Drupal\effective_activism\Entity\DataType;
use Drupal\simpletest\WebTestBase;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;

/**
 * Function tests for the Data entity.
 *
 * @group activeforanimals
 */
class DataTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'activeforanimals';

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'effective_activism',
  ];

  /**
   * The DataType config entity.
   *
   * @var \Drupal\effective_activism\Entity\DataType
   */
  private $dataType;

  /**
   * The data entity.
   *
   * @var \Drupal\effective_activism\Entity\Data
   */
  private $data;

  /**
   * Run test.
   */
  public function testDo() {
    // Create data type.
    $this->dataType = DataType::create([
      'id' => 'data_type_test',
      'label' => 'Test',
    ]);
    $this->dataType->save();
    // Add an integer field to the data type.
    $field_name = 'field_integer_input';
    $entity_type_id = 'data';
    $bundle = 'data_type_test';
    $label = 'Integer input';
    // Check if field exists and create as necessary.
    $field_storage = FieldStorageConfig::loadByName($entity_type_id, $field_name);
    if (empty($field_storage)) {
      $field_storage = FieldStorageConfig::create([
        'field_name' => $field_name,
        'entity_type' => $entity_type_id,
        'type' => 'integer',
        'cardinality' => 1,
        'module' => 'core',
        'settings' => ['min' => '0'],
      ])->save();
    }
    $field = FieldConfig::loadByName($entity_type_id, $bundle, $field_name);
    if (empty($field)) {
      $field = FieldConfig::create([
        'field_name' => $field_name,
        'entity_type' => $entity_type_id,
        'bundle' => $bundle,
        'label' => $label,
      ]);
      $field
        ->setRequired(TRUE)
        ->save();
    }
    // Form display settings for field_integer_input.
    entity_get_form_display($entity_type_id, $bundle, 'default')
      ->setComponent('field_integer_input', [
        'type' => 'number',
      ])
      ->save();
    // View display settings for field_integer_input.
    entity_get_display($entity_type_id, $bundle, 'default')
      ->setComponent('field_integer_input', [
        'type' => 'number_integer',
      ])
      ->save();
    // Create a data content entity and test with random value.
    $random_value = rand();
    $this->data = Data::create([
      'type' => 'data_type_test',
      'user_id' => 1,
      'field_integer_input' => $random_value,
    ]);
    $value = $this->data->get('field_integer_input')->getValue();
    $this->assertEqual($value[0]['value'], $random_value);
  }

}
