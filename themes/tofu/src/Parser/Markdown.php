<?php

namespace Drupal\tofu\Parser;

use Drupal\filter\Plugin\FilterInterface;
use Parsedown;

/**
 * Parses markdown and translates it to a Drupal page structure.
 */
class Markdown {

  private $parser;

  private $raw_content;

  private $structure;

  /**
   * Constructor.
   *
   * @param string $filepath
   *   The file path of the markdown document to render.
   */
  public function __construct($filepath) {
    $this->raw_content = file_get_contents($filepath);
    $this->parser = new Parsedown();
  }

  /**
   * Returns a markdown document rendered as HTML.
   *
   * @return string
   *   HTML from a markdown document.
   */
  public function getContent() {
    return $this->parse();
  }

  /**
   * Renders HTML from markdown content.
   *
   * @return string
   *   Derived HTML.
   */
  private function parse() {
    return $this->parser->text($this->raw_content);
  }

}
