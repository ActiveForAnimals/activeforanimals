<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;

/**
 * Preprocessor for GroupOverview.
 */
class GroupOverviewPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $image_controller = new ImageController();
    $field_controller = new FieldController();
    $element_controller = new ElementController();
    $group_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      $group_overview_link = new Url(
        'entity.organization.groups', [
          'organization' => $this->variables['elements']['#storage']['entities']['organization']->id(),
        ]);
    }
    $this->variables['content']['title'] = $element_controller->view(t('Groups'), 'title', $group_overview_link);
    $this->variables['content']['create_link'] = $element_controller->view(t('Create group'), 'add_group', new Url('activeforanimals.group.create'));
    $this->variables['content']['empty'] = t('No groups created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['groups'] as $group) {
      $group_elements = [];
      $group_link = new Url(
        'entity.group.canonical', [
          'group' => $group->id(),
        ]);
        if (!$group->get('logo')->isEmpty()) {
          $group_elements['logo'] = $image_controller->view($group->get('logo')->entity->getFileUri(), 'logo', ImageController::LOGO_200X200, $group_link);
        }
        if (!$group->get('title')->isEmpty()) {
          $group_elements['title'] = $field_controller->view($group->get('title'), $group_link);
        }
        if (!$group->get('location')->isEmpty()) {
          $group_elements['location'] = $field_controller->view($group->get('location'));
        }
        $group_elements['event_count'] = $element_controller->view(t('Events (@event_count)', [
          '@event_count' => count(GroupHelper::getEvents($group, 0, 0, FALSE)),
        ]), 'event_count');
        $this->variables['content']['groups'][] = $group_elements;
    }
    $this->variables['content']['pager'] = [
      '#type' => 'pager',
    ];
    return $this->variables;
  }

}
