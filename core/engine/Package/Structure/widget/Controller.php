<?php
namespace %moduleNamespace%\Widget\%nameUpper%;

use Core\Form\CoreForm;
use Engine\Widget\Controller as WidgetController;

/**
 * Widget %nameUpper%.
 *
 * @category PhalconEye\Widget
 * @package  Widget
 */
class Controller extends WidgetController
{
    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {

    }

    /**
     * Action for management from admin panel.
     *
     * @return CoreForm
     */
    public function adminAction()
    {
        $form = new CoreForm();

        return $form;
    }

    /**
     * Check if this widget must be cached.
     *
     * @return bool
     */
    public function isCached()
    {
        return false;
    }

    /**
     * What cache lifetime will be for this widget.
     *
     * @return int
     */
    public function cacheLifeTime()
    {
        return 300;
    }
}