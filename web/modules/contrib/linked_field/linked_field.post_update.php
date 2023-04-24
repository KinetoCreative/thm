<?php

/**
 * @file
 * Post update functions for Linked Field module.
 */

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Update all related display modes.
 */
function linked_field_post_update_update_related_display_modes(&$sandbox) {

  $entity_types = \Drupal::entityTypeManager()->getDefinitions();
  /** @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface $bundle_info_service */
  $bundle_info_service = \Drupal::service('entity_type.bundle.info');
  /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository */
  $entity_display_repository = \Drupal::service('entity_display.repository');

  foreach ($entity_types as $entity_type_id => $entity_type) {

    if ($entity_type instanceof ContentEntityTypeInterface) {
      $bundle_info = $bundle_info_service->getBundleInfo($entity_type_id);

      foreach ($bundle_info as $bundle => $bundle_data) {
        $view_modes = $entity_display_repository
          ->getViewModeOptionsByBundle($entity_type_id, $bundle);

        foreach ($view_modes as $view_mode => $view_mode_data) {
          $display = $entity_display_repository
            ->getViewDisplay($entity_type_id, $bundle, $view_mode);

          // Re-Save display to force applying
          // Linked Field configuration schema.
          if ($display instanceof EntityInterface) {
            $display->save();
          }
        }
      }
    }
  }
}
