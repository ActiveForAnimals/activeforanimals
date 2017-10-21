<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\effective_activism\ContentMigration\Export\CSV\CSVParser as ExportCSVParser;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Entity\Export;
use Drupal\user\Entity\User;

/**
 * Creates a test export.
 */
class CreateExport {

  private $group;

  private $organizer;

  private $file;

  /**
   * Constructor.
   *
   * @param \Drupal\effective_activism\Entity\Group $group
   *   The group the export belongs to.
   * @param \Drupal\user\Entity\User $organizer
   *   The organizer of the group.
   */
  public function __construct(Group $group, User $organizer) {
    $this->group = $group;
    $this->organizer = $organizer;
  }

  /**
   * Create import.
   */
  public function execute() {
    $export = Export::create([
      'type' => 'csv',
      'user_id' => $this->organizer->id(),
      'parent' => $this->group->id(),
    ]);
    $export->save();
    $rows = [];
    $csvParser = new ExportCSVParser($this->group, $export);
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
