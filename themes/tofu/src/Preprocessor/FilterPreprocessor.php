<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\tofu\Constant;

/**
 * Preprocessor for Filter entities.
 */
class FilterPreprocessor extends Preprocessor implements PreprocessorInterface {

  const EVENT_LIST_LIMIT = 3;

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $filter = $this->variables['elements']['#filter'];
    $this->variables['content']['organization'] = $filter->get('organization')->isEmpty() ? NULL : $this->wrapField($filter->get('organization'));
    $this->variables['content']['title'] = $filter->get('name')->isEmpty() ? NULL : $this->wrapField($filter->get('name'));
    // Add manager links.
    if (AccessControl::isManager($filter->get('organization')->entity)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.filter.edit_form', [
          'organization' => PathHelper::transliterate($filter->get('organization')->entity->label()),
          'filter' => $filter->id(),
        ]
      ));
      $publish_state = $filter->isPublished() ? t('Unpublish') : t('Publish');
      $this->variables['content']['links']['publish'] = $this->wrapElement($publish_state, 'publish', new Url(
        'entity.filter.publish_form', [
          'organization' => PathHelper::transliterate($filter->get('organization')->entity->label()),
          'filter' => $filter->id(),
        ]
      ));
    }
    return $this->variables;
  }

}
