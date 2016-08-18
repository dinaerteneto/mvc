<?php 

namespace classes;

class AbstractController {

	protected function render($view, $params = []) {

      	extract($params);

        ob_start();
        include( __DIR__."/../views/{$GLOBALS['CONTROLLER']}/{$view}.php" );
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
        return $content;

	}

}