<?php
namespace EACHeRepublica\Lib;

/**
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 */
class View
{
    public function setVars(array $vars)
    {

    }

    public function render($view)
    {
        $content = null;

        if (file_exists(VIEW_DIR.DS.$view)) {
            ob_start();
            require VIEW_DIR.DS.$view;
            $content = ob_get_clean();
        }

         return $content;
    }
}
