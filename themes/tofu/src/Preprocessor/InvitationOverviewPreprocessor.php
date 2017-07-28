<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ElementController;

/**
 * Preprocessor for InvitationOverview.
 */
class InvitationOverviewPreprocessor extends Preprocessor implements PreprocessorInterface {

  const DATE_FORMAT = 'F jS, Y';

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $element_controller = new ElementController();
    foreach ($this->variables['elements']['#storage']['invitations'] as $invitation) {
      $invitation_elements = [];
      $invitation_elements['email_address'] = $element_controller->view($invitation->email, 'invitation_email');
      $invitation_elements['timestamp'] = $element_controller->view(t('Invited on @date', [
        '@date' => date(self::DATE_FORMAT, $invitation->created),
      ]), 'invitation_timestamp');
      $invitation_elements['remove'] = $element_controller->view(t('Remove'), 'invitation_remove', Url::fromRoute('effective_activism.invitation.remove', ['invitation' => $invitation->id]));
      $this->variables['content']['invitations'][] = $invitation_elements;
    }
    $this->variables['content']['empty_message'] = t('No current invitations');
    $this->variables['content']['title'] = t('Current invitations');
    $this->variables['content']['pager'] = [
      '#type' => 'pager',
    ];
    return $this->variables;
  }

}
