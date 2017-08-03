<?php

namespace Drupal\tofu;

use Drupal\activeforanimals\Controller\FrontPageController;
use Drupal\activeforanimals\Controller\StaticPageController;
use Drupal\activeforanimals\Form\BetaSignupForm;
use Drupal\effective_activism\Helper\ListBuilder\OrganizationListBuilder;
use Drupal\effective_activism\Helper\ListBuilder\ResultTypeListBuilder;
use Drupal\effective_activism\Controller\Overview\GroupOverviewController;
use Drupal\effective_activism\Controller\Overview\EventListController;
use Drupal\effective_activism\Controller\Overview\EventOverviewController;
use Drupal\effective_activism\Controller\Overview\GroupListController;
use Drupal\effective_activism\Controller\Overview\ImportOverviewController;
use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Misc\HeaderMenuController;
use Drupal\effective_activism\Controller\Misc\InvitationController;
use Drupal\effective_activism\Controller\Misc\OrganizerToolboxController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;
use Drupal\effective_activism\Controller\ResultOverviewController;
use Drupal\effective_activism\Form\Invitation\InvitationForm;

/**
 * Provides constants.
 */
class Constant {

  const MACHINE_NAME = 'tofu';

  /**
   * Element templates.
   */
  const CONTROLLER_TEMPLATES = [
    BetaSignupForm::THEME_ID,
    'inline_entity_form_data',
    'inline_entity_form_person',
    'inline_entity_form_result',
    'event',
    'import',
    'group',
    'organization',
    'organization_group_overview',
    ContactInformationController::THEME_ID,
    EventListController::THEME_ID,
    EventOverviewController::THEME_ID,
    FrontPageController::THEME_ID,
    GroupListController::THEME_ID,
    GroupOverviewController::THEME_ID,
    HeaderMenuController::THEME_ID,
    InvitationController::THEME_ID,
    ImportOverviewController::THEME_ID,
    ManagementToolboxController::THEME_ID,
    OrganizationListBuilder::THEME_ID,
    OrganizerToolboxController::THEME_ID,
    ResultOverviewController::THEME_ID,
    ResultTypeListBuilder::THEME_ID,
    StaticPageController::THEME_ID,
  ];

  /**
   * Form templates.
   */
  const FORM_TEMPLATES = [
    InvitationForm::FORM_ID,
    'result',
    'event',
    'group',
    'organization',
    'result_type',
    'user',
  ];

  /**
   * Entity types.
   */
  const ENTITY_TYPES = [
    'event',
    'import',
    'group',
    'organization',
    'user',
  ];

  /**
   * Inline entity form format.
   */
  const INLINE_ENTITY_FORM_FORMAT = '';

  /**
   * Form templates.
   */
  const MANAGEMENT_SUBPATH = [
    'edit',
    'add',
  ];

}
