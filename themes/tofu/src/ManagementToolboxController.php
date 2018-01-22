<?php

namespace Drupal\effective_activism\Controller\Misc;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\effective_activism\Helper\AccountHelper;

/**
 * Controller class for contact information.
 */
class ManagementToolboxController extends ControllerBase {

  const THEME_ID = 'management_toolbox';

  private $organization;

  /**
   * Cosntructor.
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
        $access = AccountHelper::isManager($this->entity);
        break;

      case 'event_template':
      case 'export':
      case 'group':
      case 'filter':
        $access = AccountHelper::isManager($this->entity->get('organization')->entity);
        break;

      case 'event':
      case 'import':
        $access = AccountHelper::isManager($this->entity->get('parent')->entity->get('organization')->entity);
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
