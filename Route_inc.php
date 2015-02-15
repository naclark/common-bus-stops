<?php
class Route {
    public $RouteID = 0;
    public $ShortName = "";
    public $Description = "";
    
    function __construct($id, $short_name, $desc) {
        $this->RouteID = (int)$id;
        $this->ShortName = $short_name;
        $this->Description = $desc;
    }
    
    function __toString() {
        return '<option value ="' . $this->RouteID . '">' . $this->ShortName . '(' 
        . $this->Description . ')</option>';
    }
}