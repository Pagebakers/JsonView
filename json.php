<?php
/**
 * JsonView
 * View for displaying json objects
 *
 * PHP Version 5
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright      Copyright (c) 2008-2010, Pagebakers
 * @link           http://www.pagebakers.nl Pagebakers
 * @author         Eelco Wiersma
 * @license        MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class JsonView extends View {
    
    public $jsonp = 'callback';
    
    public function render($action = null, $layout = 'ajax') {
    
        header("Pragma: no-cache");
        header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
        header('Content-Type: application/json');
        
        if(isset($this->viewVars['result'])) {

            // Set Bad Request header if success is false
            if(isset($this->viewVars['result']['success']) && $this->viewVars['result']['success'] == false) {
                header('HTTP/1.1 400 Bad Request');
            }
            
            if(isset($this->viewVars['result']['sessionState']) && $this->viewVars['result']['sessionState'] == 'Expired') {
                header('SessionState: Expired');
                header('HTTP/1.1 403 Forbidden');
                $this->viewVars['result']['success'] = false;
            }

            $out = json_encode($this->viewVars['result']);
            
            $callback = null;
            if(isset($_GET[$this->jsonp])) {
                $callback = $_GET[$this->jsonp];
            elseif(isset($_POST[$this->jsonp])) {
                $callback = $_POST[$this->jsonp];
            }
            
            if($callback) {
                $out = sprintf($callback . '(%s)', $out);
            }

            Configure::write('debug', 0); // Be sure not to show the parse time
            echo $out; // No need to render a view, just echo the json string
            
        }
        
    }
    
}
?>