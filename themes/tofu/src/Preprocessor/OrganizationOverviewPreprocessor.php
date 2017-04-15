<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;

class OrganizationOverviewPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $field_controller = new FieldController();
    $image_controller = new ImageController();
    $element_controller = new ElementController();
    $this->variables['content']['create_link'] = $element_controller->view(t('Create organization'), 'add_organization', new Url('activeforanimals.organization.create'));
    if (empty($this->variables['elements']['#storage']['organizations'])) {
      $this->variables['content']['empty'] = t('You are not part of any organization yet. To start, @create_new_organization or join an existing one.', [
        '@create_new_organization' => \Drupal::l(
          t('create a new organization'),
          new Url(
            'activeforanimals.organization.create'
          )
        ),
      ]);
    }
    foreach ($this->variables['elements']['#storage']['organizations'] as $organization) {
      $organization_elements = [];
      $organization_link = new Url(
        'entity.organization.canonical', [
          'organization' => $organization->id(),
        ]);
      if (!$organization->get('logo')->isEmpty()) {
        $organization_elements['logo'] = $image_controller->view($organization->get('logo')->entity->getFileUri(), 'logo', ImageController::LOGO_200X200, $organization_link);
      }
      if (!$organization->get('title')->isEmpty()) {
        $organization_elements['title'] = $field_controller->view($organization->get('title'), $organization_link);
      }
      if (!$organization->get('location')->isEmpty()) {
        $organization_elements['location'] = $field_controller->view($organization->get('location'));
      }
      $this->variables['content']['organizations'][] = $organization_elements;
    }
    return $this->variables;
  }
}
