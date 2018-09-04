<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\effective_activism\Entity\ResultType;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;

/**
 * Preprocessor for InlineEntityFormResult.
 */
class InlineEntityFormResultPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $entity_manager = Drupal::entityTypeManager();
    $result_type = ResultType::load($this->variables['form']['#bundle']);
    $this->variables['content']['title'] = $result_type->label();
    $this->variables['form']['participant_count'] = $this->wrapFormElement($this->variables['form']['participant_count'], 'participant_count');
    $this->variables['form']['duration_minutes'] = $this->wrapFormElement($this->variables['form']['duration_minutes'], 'duration_minutes');
    $this->variables['form']['duration_hours'] = $this->wrapFormElement($this->variables['form']['duration_hours'], 'duration_hours');
    $this->variables['form']['duration_days'] = $this->wrapFormElement($this->variables['form']['duration_days'], 'duration_days');
    $this->variables['form']['tags'] = $this->wrapFormElement($this->variables['form'][sprintf('tags_%d', $result_type->organization)], 'tags');
    foreach ($result_type->datatypes as $key => $value) {
      if ($key === $value) {
        $field_name = sprintf('data_%s', $value);
        $description = NULL;
        $data_entity = $this->variables['form'][$field_name]['widget'][0]['inline_entity_form']['#entity'];
        try {
          $description = $entity_manager
            ->getStorage($data_entity->getEntityType()->getBundleEntityType())
            ->load($data_entity->bundle())
            ->description;
        }
        catch (PluginNotFoundException $exception) {
        }
        catch (InvalidPluginDefinitionException $exception) {
        }
        $this->variables['form']['data_items'][] = [
          'title' => $this->variables['form'][$field_name]['widget']['#title'],
          'description' => $description,
          'field' => $this->wrapFormElement($this->variables['form'][$field_name], 'data'),
        ];
      }
    }
    return $this->variables;
  }

}
