<?php

namespace Drupal\tofu\Hook;

use Drupal\activeforanimals\Controller\FrontPageController;
use Drupal\activeforanimals\Controller\StaticPageController;
use Drupal\activeforanimals\Form\NewsletterSignUp\NewsletterSignUpForm;
use Drupal\effective_activism\Helper\ListBuilder\OrganizationListBuilder;
use Drupal\effective_activism\Helper\ListBuilder\ResultTypeListBuilder;
use Drupal\effective_activism\Helper\ListBuilder\ExportListBuilder;
use Drupal\effective_activism\Helper\ListBuilder\FilterListBuilder;
use Drupal\effective_activism\Controller\Overview\GroupOverviewController;
use Drupal\effective_activism\Controller\Overview\EventListController;
use Drupal\effective_activism\Controller\Overview\GroupListController;
use Drupal\effective_activism\Controller\Overview\EventOverviewController;
use Drupal\effective_activism\Controller\Overview\ExportListController;
use Drupal\effective_activism\Controller\Overview\ExportOverviewController;
use Drupal\effective_activism\Controller\Overview\FilterOverviewController;
use Drupal\effective_activism\Controller\Overview\ImportOverviewController;
use Drupal\effective_activism\Controller\Overview\InvitationOverviewController;
use Drupal\effective_activism\Controller\Overview\ResultOverviewController;
use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Misc\HeaderMenuController;
use Drupal\effective_activism\Controller\Misc\InvitationController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;
use Drupal\effective_activism\Controller\Misc\OrganizerToolboxController;
use Drupal\tofu\Preprocessor\ContactInformationPreprocessor;
use Drupal\tofu\Preprocessor\EventPreprocessor;
use Drupal\tofu\Preprocessor\EventFormPreprocessor;
use Drupal\tofu\Preprocessor\ExportPreprocessor;
use Drupal\tofu\Preprocessor\ExportOverviewPreprocessor;
use Drupal\tofu\Preprocessor\ExportFormPreprocessor;
use Drupal\tofu\Preprocessor\ExportListPreprocessor;
use Drupal\tofu\Preprocessor\FilterPreprocessor;
use Drupal\tofu\Preprocessor\FilterOverviewPreprocessor;
use Drupal\tofu\Preprocessor\FilterFormPreprocessor;
use Drupal\tofu\Preprocessor\FilterListPreprocessor;
use Drupal\tofu\Preprocessor\GroupFormPreprocessor;
use Drupal\tofu\Preprocessor\GroupOverviewPreprocessor;
use Drupal\tofu\Preprocessor\EventListPreprocessor;
use Drupal\tofu\Preprocessor\EventOverviewPreprocessor;
use Drupal\tofu\Preprocessor\FrontPagePreprocessor;
use Drupal\tofu\Preprocessor\GroupListPreprocessor;
use Drupal\tofu\Preprocessor\GroupPreprocessor;
use Drupal\tofu\Preprocessor\HeaderMenuPreprocessor;
use Drupal\tofu\Preprocessor\HtmlPreprocessor;
use Drupal\tofu\Preprocessor\InvitationPreprocessor;
use Drupal\tofu\Preprocessor\ImportFormPreprocessor;
use Drupal\tofu\Preprocessor\ImportOverviewPreprocessor;
use Drupal\tofu\Preprocessor\ImportPreprocessor;
use Drupal\tofu\Preprocessor\InvitationOverviewPreprocessor;
use Drupal\tofu\Preprocessor\ManagementToolboxPreprocessor;
use Drupal\tofu\Preprocessor\NewsletterSignUpFormPreprocessor;
use Drupal\tofu\Preprocessor\OrganizationFormPreprocessor;
use Drupal\tofu\Preprocessor\OrganizationListPreprocessor;
use Drupal\tofu\Preprocessor\OrganizationPreprocessor;
use Drupal\tofu\Preprocessor\OrganizerToolboxPreprocessor;
use Drupal\tofu\Preprocessor\PagePreprocessor;
use Drupal\tofu\Preprocessor\PublishGroupFormPreprocessor;
use Drupal\tofu\Preprocessor\ResultFormPreprocessor;
use Drupal\tofu\Preprocessor\ResultOverviewPreprocessor;
use Drupal\tofu\Preprocessor\ResultTypeOverviewPreprocessor;
use Drupal\tofu\Preprocessor\ResultTypeFormPreprocessor;
use Drupal\tofu\Preprocessor\StaticPagePreprocessor;
use Drupal\tofu\Preprocessor\UserFormPreprocessor;
use Drupal\tofu\Preprocessor\UserLoginFormPreprocessor;
use Drupal\tofu\Preprocessor\UserPasswordFormPreprocessor;
use Drupal\tofu\Preprocessor\UserPreprocessor;
use Drupal\tofu\Preprocessor\UserRegisterFormPreprocessor;

/**
 * Implements hook_preprocess().
 */
class PreprocessHook implements HookInterface {

  /**
   * An instance of this class.
   *
   * @var HookImplementation
   */
  private static $instance;

  /**
   * {@inheritdoc}
   */
  public static function getInstance() {
    if (!(self::$instance instanceof self)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * {@inheritdoc}
   */
  public function invoke(array $args) {
    $variables = $args['variables'];
    $hook = $args['hook'];
    switch ($hook) {
      case ContactInformationController::THEME_ID:
        $preprocessor = new ContactInformationPreprocessor($variables);
        break;

      case 'event':
        $preprocessor = new EventPreprocessor($variables);
        break;

      case 'event-form':
        $preprocessor = new EventFormPreprocessor($variables);
        break;

      case 'export':
        $preprocessor = new ExportPreprocessor($variables);
        break;

      case 'export-form':
        $preprocessor = new ExportFormPreprocessor($variables);
        break;

      case 'filter':
        $preprocessor = new FilterPreprocessor($variables);
        break;

      case 'filter-form':
        $preprocessor = new FilterFormPreprocessor($variables);
        break;

      case ExportListBuilder::THEME_ID:
        $preprocessor = new ExportListPreprocessor($variables);
        break;

      case ExportOverviewController::THEME_ID:
        $preprocessor = new ExportOverviewPreprocessor($variables);
        break;

      case ExportListController::THEME_ID:
        $preprocessor = new ExportListPreprocessor($variables);
        break;

      case EventListController::THEME_ID:
        $preprocessor = new EventListPreprocessor($variables);
        break;

      case EventOverviewController::THEME_ID:
        $preprocessor = new EventOverviewPreprocessor($variables);
        break;

      case FilterListBuilder::THEME_ID:
        $preprocessor = new FilterListPreprocessor($variables);
        break;

      case FilterOverviewController::THEME_ID:
        $preprocessor = new FilterOverviewPreprocessor($variables);
        break;

      case FrontPageController::THEME_ID:
        $preprocessor = new FrontPagePreprocessor($variables);
        break;

      case GroupOverviewController::THEME_ID:
        $preprocessor = new GroupOverviewPreprocessor($variables);
        break;

      case 'group':
        $preprocessor = new GroupPreprocessor($variables);
        break;

      case 'group-form':
        $preprocessor = new GroupFormPreprocessor($variables);
        break;

      case GroupListController::THEME_ID:
        $preprocessor = new GroupListPreprocessor($variables);
        break;

      case HeaderMenuController::THEME_ID:
        $preprocessor = new HeaderMenuPreprocessor($variables);
        break;

      case 'html':
        $preprocessor = new HtmlPreprocessor($variables);
        break;

      case InvitationController::THEME_ID:
        $preprocessor = new InvitationPreprocessor($variables);
        break;

      case 'import':
        $preprocessor = new ImportPreprocessor($variables);
        break;

      case 'import-form':
        $preprocessor = new ImportFormPreprocessor($variables);
        break;

      case ImportOverviewController::THEME_ID:
        $preprocessor = new ImportOverviewPreprocessor($variables);
        break;

      case InvitationOverviewController::THEME_ID:
        $preprocessor = new InvitationOverviewPreprocessor($variables);
        break;

      case ManagementToolboxController::THEME_ID:
        $preprocessor = new ManagementToolboxPreprocessor($variables);
        break;

      case sprintf('%s-form', NewsletterSignUpForm::FORM_ID):
        $preprocessor = new NewsletterSignUpFormPreprocessor($variables);
        break;

      case 'organization':
        $preprocessor = new OrganizationPreprocessor($variables);
        break;

      case 'organization-form':
        $preprocessor = new OrganizationFormPreprocessor($variables);
        break;

      case OrganizationListBuilder::THEME_ID:
        $preprocessor = new OrganizationListPreprocessor($variables);
        break;

      case OrganizerToolboxController::THEME_ID:
        $preprocessor = new OrganizerToolboxPreprocessor($variables);
        break;

      case 'page':
        $preprocessor = new PagePreprocessor($variables);
        break;

      case 'inline_entity_form_result':
        $preprocessor = new ResultFormPreprocessor($variables);
        break;

      case 'publish_group-form':
        $preprocessor = new PublishGroupFormPreprocessor($variables);
        break;

      case 'result_type-form':
        $preprocessor = new ResultTypeFormPreprocessor($variables);
        break;

      case ResultOverviewController::THEME_ID:
        $preprocessor = new ResultOverviewPreprocessor($variables);
        break;

      case ResultTypeListBuilder::THEME_ID:
        $preprocessor = new ResultTypeOverviewPreprocessor($variables);
        break;

      case StaticPageController::THEME_ID:
        $preprocessor = new StaticPagePreprocessor($variables);
        break;

      case 'user':
        $preprocessor = new UserPreprocessor($variables);
        break;

      case 'user-form':
        $preprocessor = new UserFormPreprocessor($variables);
        break;

      case 'user_pass-form':
        $preprocessor = new UserPasswordFormPreprocessor($variables);
        break;

      case 'user_login-form':
        $preprocessor = new UserLoginFormPreprocessor($variables);
        break;

      case 'user_register-form':
        $preprocessor = new UserRegisterFormPreprocessor($variables);
        break;

    }
    if (!empty($preprocessor)) {
      return $preprocessor->preprocess();
    }
    return $variables;
  }

}
