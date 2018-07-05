<?php

namespace Drupal\activeforanimals\ListBuilder;

use Drupal;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\effective_activism\Constant;
use ReflectionClass;

/**
 * Defines a class to build a listing of Event entities created by User.
 *
 * @ingroup activeforanimals
 */
class UserEventsListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  const CACHE_MAX_AGE = Cache::PERMANENT;

  const CACHE_TAGS = [
    Constant::CACHE_TAG_USER,
    Constant::CACHE_TAG_EVENT,
  ];

  const DEFAULT_EMPTY_TEXT = 'No events created yet.';

  const DEFAULT_LIMIT = 10;

  const DEFAULT_SORTING_PREFERENCE = FALSE;

  const DEFAULT_TITLE = 'My events';

  /**
   * The account of the user that created events.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * Empty text.
   *
   * @var string
   */
  protected $emptyText;

  /**
   * From date.
   *
   * @var \Drupal\Core\Datetime\DrupalDateTime
   */
  protected $fromDate;

  /**
   * A pager index to resolve multiple pagers on a page.
   *
   * @var int
   */
  protected $pagerIndex = 0;

  /**
   * Sorting preference.
   *
   * @var bool
   */
  protected $sortAsc;

  /**
   * Title.
   *
   * @var string
   */
  protected $title;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, AccountProxyInterface $account = NULL) {
    parent::__construct($entity_type, $storage);
    $this->account = isset($account) ? $account : Drupal::currentUser();
    $this->emptyText = self::DEFAULT_EMPTY_TEXT;
    $this->limit = self::DEFAULT_LIMIT;
    $this->sortAsc = self::DEFAULT_SORTING_PREFERENCE;
    $this->title = self::DEFAULT_TITLE;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery();
    // Only show events created by the user.
    $query->condition('user_id', $this->account->id());
    // Only show events equal to or newer than this date.
    if (isset($this->fromDate)) {
      $query->condition('start_date', $this->fromDate->format(DATETIME_DATETIME_STORAGE_FORMAT), '>=');
    }
    // Sorting preference.
    if ($this->sortAsc) {
      $query->sort('start_date', 'ASC');
    }
    else {
      $query->sort('start_date', 'DESC');
    }
    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }
    $result = $query->execute();
    return $result;
  }

  /**
   * Set the empty message when there is nothing to list.
   *
   * @var string $empty
   *   The empty text to set.
   */
  public function setEmpty($empty) {
    $this->emptyText = $empty;
    return $this;
  }

  /**
   * Set a from date.
   *
   * @var \Drupal\Core\Datetime\DrupalDateTime $date
   *   The date to set.
   */
  public function setFromDate(DrupalDateTime $date) {
    $this->fromDate = $date;
    return $this;
  }

  /**
   * Setter to dynamically set limit. See https://www.drupal.org/node/2736377.
   *
   * @var int $limit
   *   The limit to set.
   */
  public function setLimit($limit) {
    $this->limit = $limit;
    return $this;
  }

  /**
   * Sets an index for the pager.
   *
   * @var int $pager_index
   *   The index to set.
   *
   * @return self
   *   This instance.
   */
  public function setPagerIndex($pager_index) {
    $this->pagerIndex = $pager_index;
    return $this;
  }

  /**
   * Determine if sorting by start date should be ascending or descending.
   *
   * @var bool $preference
   *   Whether or not to sort start date ascending.
   */
  public function setSortAsc($preference) {
    $this->sortAsc = $preference;
    return $this;
  }

  /**
   * Sets the title.
   *
   * @var string $title
   *   The title to set.
   */
  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['#theme'] = (new ReflectionClass($this))->getShortName();
    $build['#storage']['entities']['events'] = $this->load();
    $build['content']['title'] = $this->title;
    $build['content']['empty'] = $this->emptyText;
    $build['#cache'] = [
      'max-age' => self::CACHE_MAX_AGE,
      'tags' => self::CACHE_TAGS,
    ];
    $build['pager'] = [
      '#type' => 'pager',
      '#element' => $this->pagerIndex,
    ];
    return $build;
  }

}
