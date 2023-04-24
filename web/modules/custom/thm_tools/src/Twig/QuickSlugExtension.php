<?php 

namespace Drupal\thm_tools\Twig;

class QuickSlugExtension extends \Twig_Extension{
    /**
    * {@inheritdoc}
    * This function must return the name of the extension. It must be unique.
    */
    public function getName() {
        return 'quick_slug';
    }

    /**
    * In this function we can declare the extension function
    */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('quick_slug', 
                array($this, 'quick_slug'),
                array('is_safe' => array('html')
            )),
        );
    }

     /**
   * Provides function to programmatically rendering a menu
   *
   * @param String $menu_name
   *   The machine configuration id of the menu to render
   */
  public function quick_slug($text) {
    $slug = str_replace(array(' '),'-',$text);
    $slug = str_replace(array('&'),'',$slug);
    $slug = strtolower($slug);
    return  $slug;
  }
 }