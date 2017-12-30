<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;

/**
 * Preprocessor for GroupList.
 */
class OrganizationListPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $image_controller = new ImageController();
    $field_controller = new FieldController();
    $element_controller = new ElementController();
    $this->variables['content']['title'] = $element_controller->view(t('Organizations'), 'title');
    $this->variables['content']['empty'] = t('No organizations created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['organizations'] as $organization) {
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
      $organization_elements['group_count'] = $element_controller->view(t('Groups (@group_count)', [
        '@group_count' => count(OrganizationHelper::getGroups($organization, 0, 0, FALSE)),
      ]), 'event_count');
      $this->variables['content']['organizations'][] = $organization_elements;
    }
    return $this->variables;
  }

}
