<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateEvent;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\Group;
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
    foreach (Constant::DEFAULT_RESULT_TYPES as $import_name => $options) {
      $this->drupalGet(sprintf('%s/edit', $this->event->toUrl()->toString()));
      $this->assertResponse(200);
      $this->assertFieldByXPath('//input[@type="submit" and @value="Add new result"]', NULL, 'Button to add new results exists');
      $result_type = ResultTypeHelper::getResultTypeByImportName($import_name, $this->organization->id());
      $this->drupalPostAjaxForm(NULL, [
        'results[actions][bundle]' => $result_type->id(),
      ], $this->getButtonName('//input[@type="submit" and @value="Add new result"]'));
      $this->assertResponse(200);
      $data_fields = [];
      foreach ($options['datatypes'] as $key => $value) {
        $data_fields[sprintf('results[0][data_%s][0][inline_entity_form][field_%s][0][value]', $key, $key)] = '300';
      }
      $this->drupalPostAjaxForm(NULL, array_merge([
        sprintf('results[0][participant_count][0][value]') => '1',
        sprintf('results[0][duration_minutes]', $import_name) => '30',
        sprintf('results[0][duration_hours]', $import_name) => '1',
        sprintf('results[0][duration_days]', $import_name) => '0',
        sprintf('results[0][tags_1][0][target_id]', $import_name) => 'tag_test',
      ], $data_fields), $this->getButtonName('//input[@type="submit" and @value="Create result"]'));
      $this->assertResponse(200);
      $this->drupalPostForm(sprintf('%s/edit', $this->event->toUrl()->toString()), [], t('Save'));
      $this->assertText('Saved the event.', 'Added a new event entity.');
    }
  }

  /**
   * Gets IEF button name.
   *
   * @param string $xpath
   *   Xpath of the button.
   *
   * @return string
   *   The name of the button.
   */
  private function getButtonName($xpath) {
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
    return $retval;
  }

}
