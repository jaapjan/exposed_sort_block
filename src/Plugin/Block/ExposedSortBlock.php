<?php

/**
 * @file
 * Contains \Drupal\views\Plugin\Block\ExposedSortBlock.
 */

namespace Drupal\exposed_sort_block\Plugin\Block;
use Drupal\Core\Cache\Cache;
use Drupal\views\Plugin\Block\ViewsExposedFilterBlock;

/**
 * Provides a 'Exposed Sort' block.
 *
 * @Block(
 *   id = "exposed_sort_block",
 *   admin_label = @Translation("Exposed Sort Block"),
 *   deriver = "Drupal\exposed_sort_block\Plugin\Derivative\ExposedSortBlock"
 * )
 */
class ExposedSortBlock extends ViewsExposedFilterBlock {

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = $this->view->display_handler->getCacheMetadata()->getCacheContexts();
    return Cache::mergeContexts(parent::getCacheContexts(), $contexts);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Avoid interfering with the admin forms.
    $route_name = \Drupal::routeMatch()->getRouteName();
    if (strpos($route_name, 'views_ui.') === 0) {
      return;
    }
    $this->view->initHandlers();

    if ($this->view->display_handler->usesExposed() && $this->view->display_handler->getOption('exposed_block')) {
      $exposed_form = $this->view->display_handler->getPlugin('exposed_form');
      $output = $exposed_form->renderExposedSortForm(TRUE);
    }

    // Before returning the block output, convert it to a renderable array with
    // contextual links.
    $this->addContextualLinks($output, 'exposed_filter');

    return $output;
  }

}
