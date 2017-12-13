<?php

namespace Drupal\activeforanimals\Tests;

use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateEvent;
use Drupal\activeforanimals\Tests\Helper\CreateGroup;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Helper\ResultTypeHelper;
use Drupal\effective_activism\Constant;
use Drupal\simpletest\WebTestBase;

/**
 * Event results with varying levels of available result types.
 *
 * @group activeforanimals
 */
class EventResultExtendedTest extends WebTestBase {

  const CREATE_EVENT_PATH = 'create-event';

  const GROUP_TITLE_1 = 'Group 1';

  const GROUP_TITLE_2 = 'Group 2';

  const WRONG_DEFAULT_RESULT_TYPE_LABEL = 'Fundraising';

  const DEFAULT_RESULT_TYPE_LABEL = 'Leafleting';

  const DEFAULT_RESULT_TYPE_IMPORTNAME = 'leafleting';

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
   * Group 1.
   *
   * @var \Drupal\effective_activism\Entity\Group
   */
  private $group1;

  /**
   * Group 2.
   *
   * @var \Drupal\effective_activism\Entity\Group
   */
  private $group2;

  /**
   * Manager.
   *
   * @var User
   */
  private $manager;

  /**
   * Organizer.
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
    // Remove default group for organization.
    $group = Group::load(1);
    $group->delete();
    // Add a single result type for group 1 and 2.
    $this->group1 = (new CreateGroup($this->organization, $this->organizer, 'Group 1'))->execute();
    $this->group2 = (new CreateGroup($this->organization, $this->organizer, 'Group 2'))->execute();
    $result_type = ResultTypeHelper::getResultTypeByImportName(self::DEFAULT_RESULT_TYPE_IMPORTNAME, $this->organization->id());
    $result_type->groups = [
      $this->group1->id() => $this->group1->id(),
      $this->group2->id() => $this->group2->id(),
    ];
    $result_type->save();
  }

  /**
   * Run test.
   */
  public function testDo() {
    // See https://www.drupal.org/project/inline_entity_form/issues/2929727.
    $this->drupalLogin($this->organizer);
    $this->drupalGet(self::CREATE_EVENT_PATH);
    $this->drupalPostAjaxForm(NULL, [
      'title[0][value]' => '',
    ], $this->getElementName('//input[@type="submit" and @value="Add new result"]'));
    $this->assertResponse(200);
    $this->assertNoText(self::WRONG_DEFAULT_RESULT_TYPE_LABEL, 'Wrong result type not found');
    $this->assertText(self::DEFAULT_RESULT_TYPE_LABEL, 'Correct result type found');
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
