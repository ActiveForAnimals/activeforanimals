<?php

namespace Drupal\tofu\Parser;

use Parsedown;

/**
 * Parses markdown and translates it to a Drupal page structure.
 */
class Markdown {

  private $parser;

  /**
   * Constructor.
   *
   * @param string $filepath
   *   The path to the markdown document to render.
   */
  public function __construct() {
    $this->parser = new Parsedown();
  }

  /**
   * Returns a markdown document rendered as HTML.
   *
   * @return string
   *   HTML from a markdown document.
   */
  public function render($filepath) {
    $raw = file_get_contents($filepath);
    return $this->parser->text($raw);
  }

}
