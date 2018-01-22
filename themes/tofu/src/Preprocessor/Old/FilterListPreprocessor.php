<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for FilterList.
 */
class FilterListPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $field_controller = new FieldController();
    $button_controller = new ButtonController();
    $element_controller = new ElementController();
    $filter_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      $filter_overview_link = new Url(
      'entity.organization.filters', [
        'organization' => $this->variables['elements']['#storage']['entities']['organization']->id(),
      ]);
    }
    $this->variables['content']['title'] = $element_controller->view(t('Filters'), 'title', $filter_overview_link);
    $this->variables['content']['create_link'] = $element_controller->view(t('Create filter'), 'add_filter', new Url('activeforanimals.filter.create'));
    $this->variables['content']['empty'] = t('No filters created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['filters'] as $filter) {
      $filter_elements = [];
      $filter_link = new Url(
        'entity.filter.canonical', [
          'filter' => $filter->id(),
        ]);
      $organization_link = new Url(
        'entity.organization.canonical', [
          'organization' => $filter->organization->entity->id(),
        ]);
      $filter_elements['name'] = $field_controller->view($filter->name);
      if (!$filter->get('organization')->isEmpty()) {
        $filter_elements['organization'] = $field_controller->view($filter->organization, $organization_link);
      }
      $filter_elements['more_info'] = $button_controller->view(t('More info'), 'more_info', $filter_link);
      $this->variables['content']['filters'][] = $filter_elements;
    }
    return $this->variables;
  }

}
