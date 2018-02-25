<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating filters.
 *
 * @group activeforanimals
 */
class FilterTest extends WebTestBase {

  const ADD_FILTER_PATH = '/o/%s/filters/add';
  const TITLE = 'Test filter';

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
   * @var Organization
   */
  private $organization;

  /**
   * The test manager.
   *
   * @var User
   */
  private $manager;

  /**
   * The test organizer.
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
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf(
      self::ADD_FILTER_PATH,
      PathHelper::transliterate($this->organization->label())
    ));
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'name[0][value]' => self::TITLE,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(sprintf('Created the %s filter.', self::TITLE), 'Created a new filter.');
  }

}
