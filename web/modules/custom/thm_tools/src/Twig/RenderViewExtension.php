<?php 

namespace Drupal\thm_tools\Twig;

class RenderViewExtension extends \Twig_Extension{
    /**
    * {@inheritdoc}
    * This function must return the name of the extension. It must be unique.
    */
    public function getName() {
        return 'render_view';
    }

    /**
    * In this function we can declare the extension function
    */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('render_view', 
                array($this, 'render_view'),
                array('is_safe' => array('html')
            )),
        );
    }

     /**
   * Provides function to programmatically rendering a menu
   *
   * @param String $view_name
   *   The machine configuration id of the menu to render
   */
  public function render_view($view_name) {
    //$bid = 'exposedformview_nameview_display_name';
    //$view_name = 'birthday_party_packages';
    //$view_name = 'views_block__birthday_party_packages_birthday_party_packages';
    $block = \Drupal\block\Entity\Block::load($view_name);
    $render = \Drupal::entityTypeManager()
            ->getViewBuilder('block')
            ->view($block);
    return $render;
  }
 }