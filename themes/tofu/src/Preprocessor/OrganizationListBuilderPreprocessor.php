<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;

/**
 * Preprocessor for OrganizationListBuilder.
 */
class OrganizationListBuilderPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['content']['title'] = $this->wrapElement(t('Organizations'), 'title');
    $this->variables['content']['empty'] = t('You are not part of any organization yet. To start, @create_new_organization or join an existing one.', [
      '@create_new_organization' => Drupal::l(
        t('create a new organization'),
        new Url(
          'entity.organization.add_form'
        )
      ),
    ]);
    foreach ($this->variables['elements']['#storage']['entities']['organizations'] as $organization_id => $organization) {
      $organization_elements = [];
      $organization_elements['logo'] = !$organization->get('logo')->isEmpty() ?  $this->wrapImage(
        $organization->get('logo')->entity->getFileUri(),
        'logo',
        self::LOGO_200X200,
        new Url('entity.organization.canonical', ['organization' => PathHelper::transliterate($organization->label())])
      ) : NULL;
      $organization_elements['title'] = $this->wrapField($organization->get('title'), new Url('entity.organization.canonical', [
        'organization' => PathHelper::transliterate($organization->label()),
      ]));
      $organization_elements['location'] = !$organization->get('location')->isEmpty() ? $this->wrapField($organization->get('location')) : NULL;
      $organization_elements['group_count'] = $this->wrapElement(t('Groups (@group_count)', [
        '@group_count' => count(OrganizationHelper::getGroups($organization, 0, 0, FALSE)),
      ]), 'event_count');
      $this->variables['content']['organizations'][] = $organization_elements;
    }
    return $this->variables;
  }

}
