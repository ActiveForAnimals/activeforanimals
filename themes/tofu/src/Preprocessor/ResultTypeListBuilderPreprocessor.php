<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Constant;
use Drupal\effective_activism\Entity\DataType;
use Drupal\effective_activism\Entity\Organization;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Helper\ResultTypeHelper;

/**
 * Preprocessor for ResultTypeListBuilder.
 */
class ResultTypeListBuilderPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $result_type_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      $result_type_overview_link = new Url(
        'entity.organization.result_types', [
          'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
        ]);
    }
    $this->variables['content']['title'] = $this->wrapElement(t('Result types'), 'title', $result_type_overview_link);
    $this->variables['content']['create_link'] = $this->wrapElement(t('Create result type'), 'add_result_type', new Url(
      'entity.result_type.add_form', [
        'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
      ]
    ));
    $this->variables['content']['empty'] = t('No result types created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['result_types'] as $result_type_id => $result_type) {
      $result_type_elements = [];
      $result_type_elements['label'] = $this->wrapElement($result_type->get('label'), 'label');
      $result_type_elements['edit_link'] = $this->wrapElement(t('Edit'), 'edit_page', new Url('entity.result_type.edit_form', [
        'organization' => PathHelper::transliterate(Organization::load($result_type->get('organization'))->label()),
        'result_type' => PathHelper::transliterate($result_type->get('importname')),
      ]));
      $result_type_elements['group_count'] = $this->wrapElement(t('Groups (@group_count)', [
        '@group_count' => in_array(Constant::RESULT_TYPE_ALL_GROUPS, $result_type->get('groups')) ? count(OrganizationHelper::getGroups(Organization::load($result_type->get('organization'), 0, 0, FALSE))) : count($result_type->get('groups')),
      ]), 'group_count');
      $event_count = count(ResultTypeHelper::getEvents($result_type, 0, 0, FALSE));
      $result_type_elements['event_count'] = $this->wrapElement(t('Events (@event_count)', [
        '@event_count' => $event_count,
      ]), 'event_count');
      if ($event_count === 0) {
        $result_type_elements['delete_link'] = $this->wrapElement(t('Delete'), 'delete', new Url('entity.result_type.delete_form', [
          'organization' => PathHelper::transliterate(Organization::load($result_type->get('organization'))->label()),
          'result_type' => PathHelper::transliterate($result_type->get('importname')),
        ]));
      }
      $data_types = DataType::loadMultiple(array_map(function ($data_type) {
        if ($data_type !== 0) {
          return $data_type;
        }
      }, $result_type->get('datatypes')));
      $data_type_labels = array_map(function ($data_type) {
        return $data_type->get('label');
      }, $data_types);
      $result_type_elements['data_types'] = $this->wrapElement(implode(', ', $data_type_labels), 'data_types');
      $this->variables['content']['result_types'][] = $result_type_elements;
    }
    return $this->variables;
  }

}
