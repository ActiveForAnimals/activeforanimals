<?php

namespace Drupal\tofu\Preprocessor;

use DOMDocument;
use DOMXPath;
use Drupal;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Xss;
use Drupal\tofu\Parser\Markdown;

/**
 * Preprocessor for static pages.
 */
class StaticPagePreprocessor extends Preprocessor implements PreprocessorInterface {

  private $structure;

  private $filepath;

  private $filename;

  private $imagepath;

  private $parser;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $variables) {
    parent::__construct($variables);
    $this->structure = new DOMDocument();
    $this->filepath = $this->variables['elements']['#filepath'];
    $this->filename = $this->variables['elements']['#filename'];
    $this->imagepath = $this->variables['elements']['#imagepath'];
    $this->parser = new Markdown();
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $path = sprintf('%s/%s', $this->variables['elements']['#filepath'], $this->variables['elements']['#filename']);
    // Suppress errors as no doctype is declared.
    $previous_value = libxml_use_internal_errors(TRUE);
    $this->structure->loadHTML($this->parser->render($path));
    libxml_use_internal_errors($previous_value);
    // Parse include files identified by HTML comments.
    $xpath = new DOMXPath($this->structure);
    foreach ($xpath->query('//comment()') as $comment) {
      $file = $comment->nodeValue;
      if (!empty($file) && file_exists(sprintf('%s/%s', $this->filepath, $file))) {
        $fragment = $this->structure->createDocumentFragment();
        $include_html = $this->parser->render(sprintf('%s/%s', $this->filepath, $file));
        $fragment->appendXML($include_html);
        $comment->parentNode->replaceChild($fragment, $comment);
      }
    }
    // Optionally add index after first paragraph, assuming introductory text.
    if ($this->structure->getElementsByTagName('h2')->length > 0) {
      $content_index = $this->structure->createElement('ul');
      $paragraphs = $this->structure->getElementsByTagName('p');
      if (!empty($paragraphs)) {
        $paragraph = $paragraphs->item(0);
        $paragraph->parentNode->insertBefore($content_index, $paragraph->nextSibling);
        $paragraph->parentNode->insertBefore($this->structure->createElement('p', t('On this page:')), $paragraph->nextSibling);
      }
      else {
        $content_index = $this->structure->appendChild($this->structure->createElement('ul'));
        $this->structure->appendChild($this->structure->createElement('p', t('On this page:')));
      }
      // Add all secondary headings to index.
      $this->anchorHeadings('h2', $content_index);
      // Add anchors to all lesser headings.
      $this->anchorHeadings('h3');
      $this->anchorHeadings('h4');
      $this->anchorHeadings('h5');
      $this->anchorHeadings('h6');
    }
    // Optionally add subsection links after index.
    $sections = [];
    foreach (scandir($this->filepath) as $filename) {
      $full_path = sprintf('%s/%s', $this->filepath, $filename);
      if (is_dir($full_path) && !in_array($filename, [
        '.',
        '..',
      ])) {
        // Strip profiles/activeforanimals/docs from full path.
        $relative_path = strtolower(str_replace('profiles/activeforanimals/docs/', '', $full_path));
        // Lookup relative path in route table.
        $router = Drupal::service('router.no_access_checks');
        $result = $router->match($relative_path);
        if ($result !== FALSE) {
          $sections[$result['_title']] = $relative_path;
        }
      }
    }
    if (!empty($sections)) {
      $section_index = $this->structure->createElement('ul');
      $paragraphs = $this->structure->getElementsByTagName('p');
      if (!empty($paragraphs->item(0))) {
        $paragraph = $paragraphs->item(0);
        $paragraph->parentNode->insertBefore($section_index, $paragraph->nextSibling);
        $paragraph->parentNode->insertBefore($this->structure->createElement('p', t('Subsections:')), $paragraph->nextSibling);
      }
      else {
        $section_index = $this->structure->appendChild($this->structure->createElement('ul'));
        $this->structure->appendChild($this->structure->createElement('p', t('Subsections:')));
      }
      foreach ($sections as $section => $path) {
        $link = $this->structure->appendChild($this->structure->createElement('a', $section));
        $link->setAttribute('href', sprintf('/%s', $path));
        $li = $this->structure->createElement('li');
        $li->appendChild($link);
        $section_index->appendChild($li);
      }
    }
    // Replace image paths.
    foreach ($this->structure->getElementsByTagName('img') as $image) {
      $src = $image->getAttribute('src');
      $image->setAttribute('src', sprintf('%s/%s', $this->imagepath, $src));
    }
    $unsafe_html = $this->structure->saveHTML();
    $this->variables['content'] = [
      '#type' => 'markup',
      '#markup' => Xss::filterAdmin($unsafe_html),
    ];
    return $this->variables;
  }

  /**
   * Adds anchors to headings.
   *
   * @param string $heading_tag
   *   A tag such as 'h2' to add anchors to.
   * @param DOMElement $index
   *   An optional index to add links to anchors.
   */
  private function anchorHeadings($heading_tag, DOMElement $index = NULL) {
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
