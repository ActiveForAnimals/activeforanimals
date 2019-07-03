<?php

namespace Drupal\tofu\Parser;

use Parsedown;

/**
 * Parses markdown and translates it to a Drupal page structure.
 */
class Markdown {

  /**
   * Parser.
   *
   * @var \Parsedown
   */
  private $parser;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->parser = new Parsedown();
  }

  /**
   * Returns a markdown document rendered as HTML.
   *
   * @param string $filepath
   *   The path to the markdown document to render.
   *
   * @return string
   *   HTML from a markdown document.
   */
  public function render($filepath) {
    $raw = file_get_contents($filepath);
    return $this->parser->text($raw);
  }

}
