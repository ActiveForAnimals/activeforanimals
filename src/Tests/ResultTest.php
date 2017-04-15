<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateData;
use Drupal\activeforanimals\Tests\Helper\CreateGroup;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating results.
 *
 * @group activeforanimals
 */
class ResultTest extends WebTestBase {

  const ADD_RESULT_PATH = 'manage/result-types/add'; //'result-types/create';
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
  private $data_type;

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
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $this->group = (new CreateGroup($this->organization, $this->organizer))->execute();
    $this->data_type = (new CreateData())->execute();
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->manager);
    $this->drupalGet(self::ADD_RESULT_PATH);
    $this->assertResponse(200);
    // Create a result type.
    $this->drupalPostAjaxForm(NULL, [
      'organization' => $this->organization->id(),
    ], 'organization');
    $this->drupalPostForm(NULL, [
      'label' => self::LABEL,
      'importname' => self::IMPORT_NAME,
      'description' => self::DESCRIPTION,
      sprintf('datatypes[%s]', CreateData::ID) => CreateData::ID,
      'organization' => $this->organization->id(),
      'groups[]' => $this->group->id(),
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created the Test Result type.', 'Added a new result type.');
  }

}
