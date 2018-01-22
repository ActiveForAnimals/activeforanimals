<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for GroupListBuilder.
 */
class GroupListBuilderPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $group_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      //$group_overview_link = new Url(
      //  'entity.organization.groups', [
      //    'organization' => $this->variables['elements']['#storage']['entities']['organization']->id(),
      //  ]);
    }
    $this->variables['content']['title'] = $this->wrapElement(t('Groups'), 'title', $group_overview_link);
    $this->variables['content']['empty'] = t('No groups created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['groups'] as $groud_id => $group) {
      $group_elements = [];
      $group_elements['logo'] = !$group->get('logo')->isEmpty() ?  $this->wrapImage(
        $group->get('logo')->entity->getFileUri(),
        'logo',
        self::LOGO_110X110,
        new Url('entity.group.canonical', ['group' => $group->id()])
      ) : NULL;
      $group_elements['title'] = $this->wrapField($group->get('title'), new Url('entity.group.canonical', [
        'organization_title' => PathHelper::transliterate($group->organization->entity->label()),
        'group_title' => PathHelper::transliterate($group->label()),
      ]));
      $group_elements['location'] = !$group->get('location')->isEmpty() ? $this->wrapField($group->get('location')) : NULL;
      $group_elements['event_count'] = $this->wrapElement(t('Events (@event_count)', [
        '@event_count' => count(GroupHelper::getEvents($group, 0, 0, FALSE)),
      ]), 'event_count');
      $this->variables['content']['groups'][] = $group_elements;
    }
    return $this->variables;
  }

}
