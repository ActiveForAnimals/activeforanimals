<?php

namespace Drupal\tofu;

use Drupal\activeforanimals\Controller\FrontPageController;
use Drupal\activeforanimals\Controller\StaticPageController;
use Drupal\effective_activism\Helper\ListBuilder\OrganizationListBuilder;
use Drupal\effective_activism\Helper\ListBuilder\ResultTypeListBuilder;
use Drupal\effective_activism\Helper\ListBuilder\ExportListBuilder;
use Drupal\effective_activism\Controller\Overview\GroupOverviewController;
use Drupal\effective_activism\Controller\Overview\EventListController;
use Drupal\effective_activism\Controller\Overview\EventOverviewController;
use Drupal\effective_activism\Controller\Overview\EventTemplateListController;
use Drupal\effective_activism\Controller\Overview\EventTemplateOverviewController;
use Drupal\effective_activism\Controller\Overview\ExportListController;
use Drupal\effective_activism\Controller\Overview\ExportOverviewController;
use Drupal\effective_activism\Controller\Overview\FilterListController;
use Drupal\effective_activism\Controller\Overview\FilterOverviewController;
use Drupal\effective_activism\Controller\Overview\GroupListController;
use Drupal\effective_activism\Controller\Overview\ImportOverviewController;
use Drupal\effective_activism\Controller\Overview\InvitationOverviewController;
use Drupal\effective_activism\Controller\Overview\ResultOverviewController;
use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Misc\HeaderMenuController;
use Drupal\effective_activism\Controller\Misc\InvitationController;
use Drupal\effective_activism\Controller\Misc\OrganizerToolboxController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;
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
    'inline_entity_form_data',
    'inline_entity_form_person',
    'inline_entity_form_result',
    'event',
    'event-template',
    'export',
    'filter',
    'import',
    'group',
    'organization',
    'organization_group_overview',
    ContactInformationController::THEME_ID,
    EventListController::THEME_ID,
    EventOverviewController::THEME_ID,
    EventTemplateOverviewController::THEME_ID,
    EventTemplateListController::THEME_ID,
    ExportOverviewController::THEME_ID,
    ExportListController::THEME_ID,
    ExportListBuilder::THEME_ID,
    FilterOverviewController::THEME_ID,
    FilterListController::THEME_ID,
    FrontPageController::THEME_ID,
    GroupListController::THEME_ID,
    GroupOverviewController::THEME_ID,
    HeaderMenuController::THEME_ID,
    InvitationController::THEME_ID,
    InvitationOverviewController::THEME_ID,
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
    'event',
    'event_template',
    'export',
    'filter',
    'group',
    'import',
    InvitationForm::FORM_ID,
    'organization',
    'result',
    'result_type',
    'user',
  ];

  /**
   * Entity types.
   */
  const ENTITY_TYPES = [
    'event',
    'event_template',
    'export',
    'filter',
    'group',
    'import',
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
