<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\effective_activism\ContentMigration\Export\CSV\CSVParser as ExportCSVParser;
use Drupal\effective_activism\Entity\Export;
use Drupal\effective_activism\Entity\Filter;
use Drupal\effective_activism\Entity\Organization;
use Drupal\user\Entity\User;

/**
 * Creates a test export.
 */
class CreateExport {

  private $organization;

  private $filter;

  private $manager;

  private $file;

  /**
   * Constructor.
   *
   * @param \Drupal\effective_activism\Entity\Organization $organization
   *   The organization the export belongs to.
   * @param \Drupal\effective_activism\Entity\Filter $filter
   *   The filter to use with the export.
   * @param \Drupal\user\Entity\User $manager
   *   The manager of the organization.
   */
  public function __construct(Organization $organization, Filter $filter, User $manager) {
    $this->organization = $organization;
    $this->filter = $filter;
    $this->manager = $manager;
  }

  /**
   * Create import.
   */
  public function execute() {
    $export = Export::create([
      'type' => 'csv',
      'user_id' => $this->manager->id(),
      'organization' => $this->organization->id(),
      'filter' => $this->filter->id(),
    ]);
    $export->save();
    $rows = [];
    $csvParser = new ExportCSVParser($this->organization, $export);
    foreach ($csvParser->getNextBatch(0) as $item) {
      $rows[] = $csvParser->processItem($item);
    }
    $headers = ExportCSVParser::buildHeaders($rows);
    $csv = ExportCSVParser::convert($rows, $headers);
    // Save CSV string to file and attach it to export entity.
    $file = file_save_data($csv);
    $export->set('field_file_csv', $file->id())->save();
    return $export;
  }

}
