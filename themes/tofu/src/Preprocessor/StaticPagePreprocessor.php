<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\tofu\Parser\Markdown;

/**
 * Preprocessor for static pages.
 */
class StaticPagePreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    if (!empty($this->variables['elements']['#filepath'])) {
      $parser = new Markdown(
        $this->variables['elements']['#filepath'],
        $this->variables['elements']['#filename'],
        $this->variables['elements']['#imagepath']
      );
      $this->variables['content'] = [
        '#type' => 'markup',
        '#markup' => $parser->getContent(),
      ];
    }
    return $this->variables;
  }

}
