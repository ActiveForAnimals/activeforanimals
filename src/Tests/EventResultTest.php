<?php

namespace Drupal\activeforanimals\Tests;

use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateEvent;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Entity\DataType;
use Drupal\effective_activism\Helper\ResultTypeHelper;
use Drupal\effective_activism\Constant;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating event results.
 *
 * @group activeforanimals
 */
class EventResultTest extends WebTestBase {

  const EDIT_EVENT_PATH = 'create-event';
  const TITLE = 'Test event';
  const DESCRIPTION = 'Test event description';
  const STARTDATE = '2016-01-01';
  const STARTDATEFORMATTED = '01/01/2016';
  const STARTTIME = '11:00';
  const ENDDATE = '2016-01-01';
  const ENDDATEFORMATTED = '01/01/2016';
  const ENDTIME = '12:00';
  const LOCATION_ADDRESS = '';
  const LOCATION_EXTRA_INFORMATION = 'Test location';

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
   * The organization to host the group.
   *
   * @var \Drupal\effective_activism\Entity\Organization
   */
  private $organization;

  /**
   * The group to host the event.
   *
   * @var \Drupal\effective_activism\Entity\Group
   */
  private $group;

  /**
   * The event to test with results.
   *
   * @var \Drupal\effective_activism\Entity\Event
   */
  private $event;

  /**
   * Container for the manager user.
   *
   * @var User
   */
  private $manager;

  /**
   * Container for the organizer user.
   *
   * @var User
   */
  private $organizer;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $this->group = Group::load(1);
    $this->event = (new CreateEvent($this->group, $this->organizer))->execute();
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->organizer);
    $entityManager = Drupal::service('entity_field.manager');
    foreach (Constant::DEFAULT_RESULT_TYPES as $import_name => $options) {
      $this->drupalGet(sprintf('%s/edit', $this->event->toUrl()->toString()));
      $this->assertResponse(200);
      $this->assertFieldByXPath('//input[@type="submit" and @value="Add new result"]', NULL, 'Button to add new results exists');
      $result_type = ResultTypeHelper::getResultTypeByImportName($import_name, $this->organization->id());
      $this->drupalPostAjaxForm(NULL, [
        'results[actions][bundle]' => $result_type->id(),
      ], $this->getElementName('//input[@type="submit" and @value="Add new result"]'));
      $this->assertResponse(200);
      // Note: Inline entity form fields can have different name attributes.
      // We search with Xpath by a subset of the name to match them all.
      $data_fields = [];
      foreach ($options['datatypes'] as $key => $value) {
        foreach ($entityManager->getFieldDefinitions('data', $key) as $field) {
          if (strpos($field->getName(), 'field_') === 0 && in_array($field->getType(), [
            'integer',
            'decimal',
          ])) {
            $datatype_name = $this->getElementName(sprintf('//input[contains(@name, "[inline_entity_form][%s][0][value]")]', $field->getName()));
            $data_fields[$datatype_name] = '300';
          }
        }
      }
      $post_data = array_merge([
        $this->getElementName('//input[contains(@name, "[participant_count][0][value]")]') => '1',
        $this->getElementName('//select[contains(@name, "[duration_minutes]")]') => '30',
        $this->getElementName('//select[contains(@name, "[duration_hours]")]') => '1',
        $this->getElementName('//select[contains(@name, "[duration_days]")]') => '0',
      ], $data_fields);
      $this->drupalPostAjaxForm(NULL, $post_data, $this->getElementName('//input[@type="submit" and @value="Create result"]'));
      $this->drupalPostForm(sprintf('%s/edit', $this->event->toUrl()->toString()), [], t('Save'));
      $this->assertResponse(200);
      $this->assertText('Saved the event.', 'Saved the event entity.');
    }
  }

  /**
   * Gets HTML element name.
   *
   * @param string $xpath
   *   Xpath of the element.
   *
   * @return string
   *   The name of the element.
   */
  private function getElementName($xpath) {
    $retval = '';
    /** @var \SimpleXMLElement[] $elements */
    if ($elements = $this->xpath($xpath)) {
      foreach ($elements[0]->attributes() as $name => $value) {
        if ($name === 'name') {
          $retval = $value;
          break;
        }
      }
    }
    return (string) $retval;
  }

}
