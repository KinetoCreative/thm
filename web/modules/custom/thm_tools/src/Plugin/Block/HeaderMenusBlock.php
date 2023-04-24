<?php

namespace Drupal\thm_tools\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\block\BlockViewBuilder;

/**
 * Provides a 'Hello' Block
 *
 * @Block(
 *   id = "header_menus",
 *   admin_label = @Translation("Header Menus Block"),
 * )
 */
class HeaderMenusBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $forms = []; 
    $variables = [];
    $own = false;

    $variables['theme_path'] = drupal_get_path('theme','thm');

    /*$con = \Drupal\block\BlockViewBuilder::lazyBuilder('homepage_restaurants', 'full');
    $d = \Drupal::service('renderer')->render($con);
    print $d->__toString();
    die();*/
    $thm_branding = \Drupal\block\Entity\Block::load('thm_branding');
    $thm_branding_content = \Drupal::entityTypeManager()
      ->getViewBuilder('block')
      ->view($thm_branding);

    $variables['thm_branding'] = \Drupal::service('renderer')->render($thm_branding_content);

    /*$restaurants_block = \Drupal\block\Entity\Block::load('views_block__homepage_restaurants_homepage_restaurants');
    $restaurants_block_content = \Drupal::entityManager()
      ->getViewBuilder('block')
      ->view($restaurants_block);
    
    $variables['restaurants_block'] = drupal_render($restaurants_block_content);

    $news_block = \Drupal\block\Entity\Block::load('views_block__news_events_homepage_homepage_news_events');
    $news_block_content = \Drupal::entityManager()
      ->getViewBuilder('block')
      ->view($news_block);
    
    $variables['news_block'] = drupal_render($news_block_content);*/


    $form_html = array(
      '#theme' => 'header_menus',
      '#cache' => array('max-age' => 0),
      '#cover_image' => '',
      '#variables' => $variables,
      //'#categories' => $categories
      /*'#account_menu' => $account_menu_html,
      '#profile' => $profile,
      '#dogs' => $dogs,
      '#logged_in' => $logged_in
      /*'#forms' => $forms,
      '#bundles' => $bundles*/
    );

    return $form_html;
  }
}
?>