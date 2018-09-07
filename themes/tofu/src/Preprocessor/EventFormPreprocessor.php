<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\effective_activism\Helper\DateHelper;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\tofu\Constant;

/**
 * Preprocessor for EventForm.
 */
class EventFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  const EVENT_LIST_LIMIT = 3;

  /**
   * Event list builder.
   *
   * @var \Drupal\effective_activism\ListBuilder\EventListBuilder
   */
  protected $eventListBuilder;

  /**
   * Group list builder.
   *
   * @var \Drupal\effective_activism\ListBuilder\GroupListBuilder
   */
  protected $groupListBuilder;

  /**
   * Constructor.
   */
  public function __construct(array $variables) {
    parent::__construct($variables);
    $this->eventListBuilder = new EventListBuilder(
      $this->entityTypeManager->getDefinition('event'),
      $this->entityTypeManager->getStorage('event')
    );
    $this->groupListBuilder = new GroupListBuilder(
      $this->entityTypeManager->getDefinition('group'),
      $this->entityTypeManager->getStorage('group')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $event = Drupal::request()->get('event');
    $group = Drupal::request()->get('group');
    $organization = Drupal::request()->get('organization');
    $now = DateHelper::getNow($organization, $group);
    $this->variables['form']['title'] = $this->wrapFormElement($this->variables['form']['title'], 'title');
    $this->variables['form']['description'] = $this->wrapFormElement($this->variables['form']['description'], 'description');
    $this->variables['form']['location'] = $this->wrapFormElement($this->variables['form']['location'], 'location');
    $this->variables['form']['start_date'] = $this->wrapFormElement($this->variables['form']['start_date'], 'start_date');
    $this->variables['form']['end_date'] = $this->wrapFormElement($this->variables['form']['end_date'], 'end_date');
    $this->variables['form']['link'] = $this->wrapFormElement($this->variables['form']['link'], 'link');
    $this->variables['form']['results'] = $this->wrapFormElement($this->variables['form']['results'], 'inline_entity_form');
    $this->variables['form']['photos'] = $this->wrapFormElement($this->variables['form']['photos'], 'photos');
    $this->variables['form']['photos']['element']['photos']['widget']['#open'] = (!empty($event) && !$event->get('photos')->isEmpty()) ? TRUE : FALSE;
    $this->variables['content']['map'] = empty($event) ? NULL : $this->wrapIframe($this->getMap($event->get('location')->getValue()), 'map');
    $this->variables['content']['groups'] = $this->groupListBuilder
      ->hideMap()
      ->render();
    $this->variables['content']['events'] = $this->eventListBuilder
      ->setLimit(self::EVENT_LIST_LIMIT)
      ->setSortAsc(TRUE)
      ->setTitle('Upcoming events')
      ->setFromDate($now)
      ->setEmpty('No upcoming events')
      ->setPagerIndex(1)
      ->render();
    $this->variables['help_button'] = [
      '#id' => 'activeforanimals_help',
      '#type' => 'button',
      '#value' => '',
      '#attributes' => [
        'title' => t('Help'),
      ],
    ];
    return $this->variables;
  }

}
