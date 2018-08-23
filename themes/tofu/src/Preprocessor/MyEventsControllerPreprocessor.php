<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\Constant;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\Helper\AccountHelper;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for MyEventsController.
 */
class MyEventsControllerPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $event_add_link = NULL;
    $event_add_from_template_link = NULL;
    $user = Drupal::currentUser();
    // If the user is organizer of one group, add 'create event' links.
    $organized_groups = AccountHelper::getGroups($user);
    if (count($organized_groups) === 1 && AccessControl::isGroupStaff($organized_groups)->isAllowed()) {
      $group = array_pop($organized_groups);
      $event_create_option = $group->organization->entity->event_creation->value;
      if (in_array($event_create_option, [
        Constant::EVENT_CREATION_ALL,
        Constant::EVENT_CREATION_EVENT,
      ])) {
        $event_add_link = new Url('entity.event.add_form', [
          'organization' => PathHelper::transliterate($group->organization->entity->label()),
          'group' => PathHelper::transliterate($group->label()),
        ]);
      }
      if (in_array($event_create_option, [
        Constant::EVENT_CREATION_ALL,
        Constant::EVENT_CREATION_EVENT_TEMPLATE,
      ])) {
        $event_add_from_template_link = new Url('entity.event.add_from_template', [
          'organization' => PathHelper::transliterate($group->organization->entity->label()),
          'group' => PathHelper::transliterate($group->label()),
        ]);
      }
    }
    $this->variables['content']['my_events'] = $this->variables['elements']['my_events'];
    $this->variables['content']['create_link'] = !empty($event_add_link) ? $this->wrapElement(t('Create event'), 'add_event', $event_add_link) : NULL;
    $this->variables['content']['create_from_template_link'] = !empty($event_add_from_template_link) ? $this->wrapElement(t('Create event from template'), 'event_template', $event_add_from_template_link) : NULL;
    return $this->variables;
  }

}
