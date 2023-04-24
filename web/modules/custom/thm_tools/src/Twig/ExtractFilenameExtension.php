<?php 

namespace Drupal\thm_tools\Twig;

class ExtractFilenameExtension extends \Twig_Extension{
    /**
    * {@inheritdoc}
    * This function must return the name of the extension. It must be unique.
    */
    public function getName() {
        return 'extract_filename';
    }

    /**
    * In this function we can declare the extension function
    */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('extract_filename', 
                array($this, 'extract_filename'),
                array('is_safe' => array('html')
            )),
        );
    }

     /**
   * Provides function to programmatically rendering a menu
   *
   * @param String $path
   *   The machine configuration id of the menu to render
   */
  public function extract_filename($path) {
    $file = basename($path);
    return urldecode($file);
  }
 }