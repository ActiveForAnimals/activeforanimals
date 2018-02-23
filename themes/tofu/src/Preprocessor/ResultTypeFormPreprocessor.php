<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\tofu\Constant;

/**
 * Preprocessor for Organization add/edit page.
 */
class ResultTypeFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  const EVENT_LIST_LIMIT = 5;

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $form = $this->variables['form'];
    $this->variables['form']['label'] = $this->wrapFormElement($form['label'], 'title');
    $this->variables['form']['importname'] = $this->wrapFormElement($form['importname'], 'importname');
    $this->variables['form']['description'] = $this->wrapFormElement($form['description'], 'description');
    $this->variables['form']['datatypes'] = $this->wrapFormElement($form['datatypes'], 'datatypes');
    $this->variables['form']['groups'] = $this->wrapFormElement($form['groups'], 'groups');
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
