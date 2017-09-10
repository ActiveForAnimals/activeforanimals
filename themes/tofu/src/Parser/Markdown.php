<?php

namespace Drupal\tofu\Parser;

use DOMDocument;
use DOMXPath;
use Parsedown;
use Drupal\Component\Utility\Html;


/**
 * Parses markdown and translates it to a Drupal page structure.
 */
class Markdown {

  private $parser;

  private $raw;

  private $structure;

  private $filepath;

  private $imagepath;

  /**
   * Constructor.
   *
   * @param string $filepath
   *   The file path of the markdown document to render.
   * @param string $imagepath
   *   The path to images used by markdown documents.
   */
  public function __construct($filepath, $filename, $imagepath) {
    $this->filepath = $filepath;
    $this->imagepath = $imagepath;
    $this->structure = new DOMDocument();
    $this->parser = new Parsedown();
    $this->raw = file_get_contents(sprintf('%s/%s', $filepath, $filename));
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
    $parsed_markdown = $this->parser->text($this->raw);
    // Suppress errors as no doctype is declared.
    $previous_value = libxml_use_internal_errors(TRUE);
    $this->structure->loadHTML($parsed_markdown);
    libxml_use_internal_errors($previous_value);
    // Parse include files.
    $xpath = new DOMXPath($this->structure);
    foreach($xpath->query('//comment()') as $comment) {
      $file = $comment->nodeValue;
      if (!empty($file)) {
        $file_content = file_get_contents(sprintf('%s/%s', $this->filepath, $file));
        if ($file_content !== FALSE && !empty($file_content)) {
          $include_fragment = $this->structure->createDocumentFragment();
          $include_fragment->appendXML($this->parser->text($file_content));
          $comment->parentNode->replaceChild($include_fragment, $comment);
        }
      }
    }
    // Optionally add index after first paragraph, assuming introductory text.
    if ($this->structure->getElementsByTagName('h2')->length > 0) {
      $index = $this->structure->createElement('ul');
      $paragraphs = $this->structure->getElementsByTagName('p');
      if (!empty($paragraphs)) {
        $paragraph = $paragraphs->item(0);
        $paragraph->parentNode->insertBefore($index, $paragraph->nextSibling);
        $paragraph->parentNode->insertBefore($this->structure->createElement('p', t('On this page:')), $paragraph->nextSibling);
      }
      else {
        $index = $this->structure->appendChild($this->structure->createElement('ul'));
        $this->structure->appendChild($this->structure->createElement('p', t('On this page:')));
      }
      // Add all secondary headings to index.
      $this->anchorHeadings('h2', $index);
      // Add anchors to all lesser headings.
      $this->anchorHeadings('h3');
      $this->anchorHeadings('h4');
      $this->anchorHeadings('h5');
      $this->anchorHeadings('h6');
    }
    // Optionally add subsection links after index.
    // TODO
    // Replace image paths.
    foreach($this->structure->getElementsByTagName('img') as $image) {
      $src = $image->getAttribute('src');
      $image->setAttribute('src', sprintf('%s/%s', $this->imagepath, $src));
    }
    return $this->structure->saveHTML();
  }

  /**
   * Adds anchors to headings.
   *
   * @param string $heading_tag
   *   A tag such as 'h2' to add anchors to.
   * @param DOMElement $index
   *   An optional index to add links to anchors.
   */
  private function anchorHeadings($heading_tag, $index = NULL) {
    foreach ($this->structure->getElementsByTagName($heading_tag) as $heading) {
      $href = Html::cleanCssIdentifier($heading->nodeValue);
      // Create anchor link.
      $anchor = $this->structure->createElement('a');
      $anchor->setAttribute('name', $href);
      $heading->parentNode->insertBefore($anchor, $heading);
      if (isset($index)) {
        // Create index link.
        $link = $this->structure->appendChild($this->structure->createElement('a', $heading->nodeValue));
        $link->setAttribute('href', sprintf('#%s', $href));
        $li = $this->structure->createElement('li');
        $li->appendChild($link);
        $index->appendChild($li);
      }
    }
  }

}
