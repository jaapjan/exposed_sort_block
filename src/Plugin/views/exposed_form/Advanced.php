<?php

/**
 * @file
 * Contains \Drupal\exposed_sort_block\Plugin\views\exposed_form\Advanced.
 */

namespace Drupal\exposed_sort_block\Plugin\views\exposed_form;

use Drupal\views\Plugin\views\exposed_form\ExposedFormPluginBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;

/**
 * Exposed form plugin that provides an advanced exposed form.
 *
 * @ingroup views_exposed_form_plugins
 *
 * @ViewsExposedForm(
 *   id = "advanced",
 *   title = @Translation("Advanced"),
 *   help = @Translation("Advanced exposed form")
 * )
 */
class Advanced extends ExposedFormPluginBase {

  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['sort_in_block'] = array('default' => $this->t('Display sort options in block'));
    return $options;
  }

  /**
   * @inheritdoc
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    // Get current settings and default values for new filters/
    $existing = $this->getCurrentSettings();

    $form['sort_in_block'] = array(
      '#type' => 'checkbox',
      // @todo '#default_value'
      '#title' => $this->t('Display sort options in block'),
      '#description' => $this->t('If this option is selected the exposed sort form elements will be exposed in a separate block.'),
    );
  }

  public function renderExposedForm($block = FALSE) {
    $form = parent::renderExposedForm($block);

    $settings = $this->getCurrentSettings();

    // Remove the sort options from this specific form.
    if ($settings['sort_in_block'] === 1) {
      $form['sort_by']['#access'] = FALSE;
      $form['sort_order']['#access'] = FALSE;
    }

    return $form;
  }

  public function renderExposedSortForm($block = FALSE) {

    // Deal with any exposed filters we may have, before building.
    $form_state = (new FormState())
      ->setStorage([
        'view' => $this->view,
        'display' => &$this->view->display_handler->display,
        'rerender' => TRUE,
      ])
      ->setMethod('get')
      ->setAlwaysProcess()
      ->disableRedirect();

    // Some types of displays (eg. attachments) may wish to use the exposed
    // filters of their parent displays instead of showing an additional
    // exposed filter form for the attachment as well as that for the parent.
    if (!$this->view->display_handler->displaysExposed() || (!$block && $this->view->display_handler->getOption('exposed_block'))) {
      $form_state->set('rerender', NULL);
    }

    if (!empty($this->ajax)) {
      $form_state->set('ajax', TRUE);
    }

    $form = \Drupal::formBuilder()->buildForm('\Drupal\exposed_sort_block\Form\ExposedSortBlockForm', $form_state);

    if (!$this->view->display_handler->displaysExposed() || (!$block && $this->view->display_handler->getOption('exposed_block'))) {
      return array();
    }
    else {
      return $form;
    }
  }

  protected function getCurrentSettings() {
    $settings = $this->options;
    return $settings;
  }

}
