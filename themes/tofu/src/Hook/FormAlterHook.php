<?php

namespace Drupal\tofu\Hook;

use Drupal\tofu\Constant;

/**
 * Implements hook_form_alter().
 */
class FormAlterHook implements HookInterface {

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
    $form = $args['form'];
    $form_id = $args['form_id'];
    switch ($form_id) {
      case 'user_login_form':
        $form['#theme'] = 'user_login-form';
        break;

      case 'user_form':
        $form['#theme'] = 'user-form';
        break;

      case 'user_pass':
        $form['#theme'] = 'user_pass-form';
        break;

      case 'user_register_form':
        $form['#theme'] = 'user_register-form';
        break;

      case 'import_csv_add_form':
      case 'import_csv_edit_form':
        $form['#theme'] = 'import-form';
        break;

      case 'export_csv_add_form':
      case 'export_csv_edit_form':
        $form['#theme'] = 'export-form';
        break;

      case 'publish_organization':
        $form['#theme'] = 'publish_organization-form';
        break;

      case 'publish_group':
        $form['#theme'] = 'publish_group-form';
        break;

      case 'publish_import':
        $form['#theme'] = 'publish_import-form';
        break;

      case 'publish_event_template':
        $form['#theme'] = 'publish_event_template-form';
        break;

      case 'publish_event':
        $form['#theme'] = 'publish_event-form';
        break;

      case 'publish_export':
        $form['#theme'] = 'publish_export-form';
        break;

      case 'publish_filter':
        $form['#theme'] = 'publish_filter-form';
        break;

    }
    foreach (Constant::FORM_TEMPLATES as $form_template) {
      if ($form_id === sprintf('%s_edit_form', $form_template) || $form_id === sprintf('%s_add_form', $form_template)) {
        $form['#theme'] = sprintf('%s-form', $form_template);
      }
    }
    return $form;
  }

}
