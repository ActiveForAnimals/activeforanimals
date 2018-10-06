<?php

namespace Drupal\tofu\Hook;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\activeforanimals\Controller\FrontPageController;
use Drupal\activeforanimals\Controller\MyEventsController;
use Drupal\activeforanimals\Controller\StaticPageController;
use Drupal\activeforanimals\Form\NewsletterSignUpForm;
use Drupal\activeforanimals\ListBuilder\UserEventsListBuilder;
use Drupal\effective_activism\Controller\InvitationController;
use Drupal\effective_activism\Form\InvitationForm;
use Drupal\tofu\Helper\ThemeHelper;
use ReflectionClass;

/**
 * Implements hook_theme().
 */
class ThemeHook implements HookInterface {

  /**
   * An instance of this class.
   *
   * @var \Drupal\tofu\Hook\HookInterface
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
    $theme = [];
    $entity_type_manager = Drupal::entityTypeManager();
    $entity_types = $entity_type_manager->getDefinitions();
    // Define template for inline result forms.
    $theme['inline_entity_form_result'] = [
      'render element' => 'form',
      'template' => sprintf('%s/%s', 'Result', ThemeHelper::convertToClassName('inline_entity_form_result')),
    ];
    $theme['inline_entity_form_data'] = [
      'render element' => 'form',
      'template' => sprintf('%s/%s', 'Data', ThemeHelper::convertToClassName('inline_entity_form_data')),
    ];
    // Define templates for entities.
    foreach ($entity_types as $machine_name => $entity_type) {
      if (
        $entity_type->getProvider() === 'effective_activism' ||
        $entity_type->id() === 'taxonomy_term'
      ) {
        $entity_class_name = ThemeHelper::convertToClassName($entity_type->id());
        $theme[$machine_name] = [
          'render element' => 'elements',
          'template' => sprintf('%s/%s', $entity_class_name, $entity_class_name),
        ];
        $handlers = $entity_type->get('handlers');
        // Add forms.
        if (!empty($handlers['form'])) {
          // Filter duplicates, as typical with add and edit form handlers.
          $form_classes = array_unique(array_values($handlers['form']));
          foreach ($form_classes as $form_class) {
            $pieces = explode('\\', $form_class);
            $theme[end($pieces)] = [
              'render element' => 'form',
              'template' => sprintf('%s/%s', $entity_class_name, end($pieces)),
            ];
          }
        }
        // Add list builders.
        if (!empty($handlers['list_builder'])) {
          $pieces = explode('\\', $handlers['list_builder']);
          $theme[end($pieces)] = [
            'render element' => 'elements',
            'template' => sprintf('%s/%s', $entity_class_name, end($pieces)),
          ];
        }
      }
    }
    // Add theme information for non-entity templates.
    foreach ([
      FrontPageController::class,
      InvitationController::class,
      InvitationForm::class,
      NewsletterSignUpForm::class,
      StaticPageController::class,
      MyEventsController::class,
      UserEventsListBuilder::class,
    ] as $class) {
      $class_information = new ReflectionClass($class);
      $short_name = $class_information->getShortName();
      $theme[$short_name] = [
        'render element' => $class_information->getParentClass()->getName() === FormBase::class ? 'form' : 'elements',
        'template' => $short_name,
      ];
    }
    // Manually add theme information for other templates.
    $theme['user'] = [
      'render element' => 'elements',
      'template' => 'User/User',
    ];
    $theme['user_form'] = [
      'render element' => 'form',
      'template' => 'User/UserForm',
    ];
    $theme['user_register_form'] = [
      'render element' => 'form',
      'template' => 'User/UserRegisterForm',
    ];
    $theme['user_login_form'] = [
      'render element' => 'form',
      'template' => 'User/UserLoginForm',
    ];
    $theme['contact_message_feedback_general_form'] = [
      'render element' => 'form',
      'template' => 'Contact/ContactMessageFeedbackGeneralForm',
    ];
    return $theme;
  }

}
