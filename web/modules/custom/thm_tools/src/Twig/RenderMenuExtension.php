<?php 

namespace Drupal\thm_tools\Twig;

class RenderMenuExtension extends \Twig_Extension{
    /**
    * {@inheritdoc}
    * This function must return the name of the extension. It must be unique.
    */
    public function getName() {
        return 'render_menu';
    }

    /**
    * In this function we can declare the extension function
    */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('render_menu', 
                array($this, 'render_menu'),
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
  public function render_menu($menu_name,$submenu = false) {
    $menu_tree = \Drupal::menuTree();

    // Build the typical default set of menu tree parameters.
    $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);
    $parameters->setMaxDepth(2);
    $parameters->expandedParents = array();
    if ($submenu != false){
        $parameters->setMinDepth(2);
        $parameters->setMaxDepth(2);
    }
    // Load the tree based on this set of parameters.
    $tree = $menu_tree->load($menu_name, $parameters);

    // Transform the tree using the manipulators you want.
    $manipulators = array(
      // Only show links that are accessible for the current user.
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      // Use the default sorting of menu links.
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    $tree = $menu_tree->transform($tree, $manipulators);

    // Finally, build a renderable array from the transformed tree.
    $menu = $menu_tree->build($tree);
    return  array('#markup' => \Drupal::service('renderer')->render($menu));
  }
 }