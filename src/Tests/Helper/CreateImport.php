<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\Component\Utility\Random;
use Drupal\effective_activism\ContentMigration\Import\CSV\CSVParser as ImportCSVParser;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Entity\Import;
use Drupal\user\Entity\User;

/**
 * Creates a test import.
 */
class CreateImport {

  private $group;

  private $organizer;

  private $file;

  /**
   * Constructor.
   *
   * @param \Drupal\effective_activism\Entity\Group $group
   *   The group the import belongs to.
   * @param \Drupal\user\Entity\User $organizer
   *   The organizer of the group.
   * @param string $csv_path
   *   The path to a CSV file to import.
   */
  public function __construct(Group $group, User $organizer, $csv_path) {
    $this->group = $group;
    $this->organizer = $organizer;
    $handle = fopen($csv_path, 'r');
    $csv = fread($handle, filesize($csv_path));
    fclose($handle);
    $this->file = file_save_data($csv);
  }

  /**
   * Create import.
   */
  public function execute() {
    $import = Import::create([
      'type' => 'csv',
      'user_id' => $this->organizer->id(),
      'parent' => $this->group->id(),
      'field_file_csv' => $this->file->id(),
    ]);
    $import->save();
    $field_file_csv = $import->get('field_file_csv')->getValue();
    $csvParser = new ImportCSVParser($field_file_csv[0]['target_id'], $this->group, $import);
    foreach ($csvParser->getNextBatch(0) as $item) {
      $csvParser->processItem($item);
    }
    return $import;
  }

}
