<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Entity\DataType;
use Drupal\effective_activism\Entity\Organization;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for ResultTypeOverview.
 */
class ResultTypeOverviewPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $field_controller = new FieldController();
    $element_controller = new ElementController();
    $button_controller = new ButtonController();
    $this->variables['content']['create_link'] = $button_controller->view(t('Create result type'), 'add_result_type', new Url('activeforanimals.result_type.create'));
    if (empty($this->variables['elements']['#storage']['result_types'])) {
      $this->variables['content']['empty'] = t('No result types found.');
    }
    foreach ($this->variables['elements']['#storage']['result_types'] as $result_type) {
      $result_type_elements = [];
      $result_type_elements['edit_link'] = $element_controller->view(t('Edit'), 'edit_page', new Url(
        'entity.result_type.edit_form', [
          'result_type' => $result_type->id(),
        ]));
        $result_type_elements['label'] = $element_controller->view($result_type->get('label'), 'label');
        $organization = Organization::load($result_type->get('organization'));
        $organization_link = new Url(
        'entity.organization.canonical', [
          'organization' => $organization->id(),
        ]);
        $result_type_elements['organization'] = $element_controller->view($organization->get('title')->getValue()[0]['value'], 'organization', $organization_link);
        $result_type_elements['group_count'] = $element_controller->view(t('Groups (@group_count)', [
          '@group_count' => count($result_type->get('groups')),
        ]), 'group_count');
        $data_types = DataType::loadMultiple(array_map(function ($data_type) {
          if ($data_type !== 0) {
            return $data_type;
          }
        }, $result_type->get('datatypes')));
        $data_type_labels = array_map(function ($data_type) {
          return $data_type->get('label');
        }, $data_types);
        $result_type_elements['data_types'] = $element_controller->view(implode(', ', $data_type_labels), 'data_types');
        $this->variables['content']['result_types'][] = $result_type_elements;
    }
    return $this->variables;
  }

}
