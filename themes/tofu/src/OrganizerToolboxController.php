<?php

namespace Drupal\effective_activism\Controller\Misc;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\effective_activism\Helper\AccountHelper;

/**
 * Controller class for contact information.
 */
class OrganizerToolboxController extends ControllerBase {

  const THEME_ID = 'organizer_toolbox';

  private $organization;

  /**
   * Constructor.
   */
  public function __construct(EntityInterface $entity) {
    $this->entity = $entity;
  }

  /**
   * Returns TRUE if current user has access, FALSE otherwise.
   *
   * @return bool
   *   Whether or not user has access.
   */
  public function access() {
    $access = FALSE;
    switch ($this->entity->getEntityTypeId()) {
      case 'organization':
        $access = AccountHelper::isOrganizerOfOrganization($this->entity);
        break;

      case 'group':
        $access = AccountHelper::isOrganizer($this->entity);
        break;

      case 'event':
      case 'import':
        $access = AccountHelper::isOrganizer($this->entity->get('parent')->entity);
        break;
    }
    return $access;
  }

  /**
   * Returns an array of fields for contact information.
   *
   * @return array
   *   A render array.
   */
  public function view() {
    $content['#theme'] = self::THEME_ID;
    $content['#storage']['entity'] = $this->entity;
    return $content;
  }

}
