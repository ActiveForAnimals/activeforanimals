<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateData;
use Drupal\activeforanimals\Tests\Helper\CreateEvent;
use Drupal\activeforanimals\Tests\Helper\CreateGroup;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\Result;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\effective_activism\Helper\ResultTypeHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating results.
 *
 * @group activeforanimals
 */
class ResultTest extends WebTestBase {

  const ADD_RESULT_PATH = '/o/%s/result-types/add';
  const DELETE_RESULT_PATH = '/o/%s/result-types/%s/delete';
  const GROUPTITLE = 'Test group';
  const LABEL = 'Test';
  const IMPORT_NAME = 'result_type_test';
  const DESCRIPTION = 'Test result type description';

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
   * The data type.
   *
   * @var DataType
   */
  private $datatype;

  /**
   * The organization to host the group.
   *
   * @var Organization
   */
  private $organization;

  /**
   * The group to host the result.
   *
   * @var Group
   */
  private $group;

  /**
   * The event to host the result.
   *
   * @var Event
   */
  private $event;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $this->group = (new CreateGroup($this->organization, $this->organizer))->execute();
    $this->datatype = (new CreateData())->execute();
    $this->event = (new CreateEvent($this->group, $this->organizer))->execute();
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf(
      self::ADD_RESULT_PATH,
      PathHelper::transliterate($this->organization->label())
    ));
    $this->assertResponse(200);
    // Create a result type.
    $this->drupalPostForm(NULL, [
      'label' => self::LABEL,
      'importname' => self::IMPORT_NAME,
      'description' => self::DESCRIPTION,
      sprintf('datatypes[%s]', CreateData::ID) => CreateData::ID,
      'groups[]' => $this->group->id(),
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created the Test Result type.', 'Added a new result type.');
    // Add an event using the result type.
    $result_type = ResultTypeHelper::getResultTypeByImportName(self::IMPORT_NAME, $this->organization->id());
    $this->event->results[] = Result::create([
      'type' => $result_type->id(),
    ]);
    $this->event->save();
    $this->drupalGet(sprintf(
      self::DELETE_RESULT_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($result_type->get('importname'))
    ));
    $this->drupalPostForm(NULL, [], t('Delete'));
    $this->assertResponse(200);
    $this->assertText('This result type is used by one event and cannot be deleted.');
  }

}
