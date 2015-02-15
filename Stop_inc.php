<?php
class Stop {
    // s.stop_id, stop_name, stop_lat, stop_lon
    public $StopID = 0;
    public $Name = "";
    public $Lat = "";
    public $Lon = "";
    
    function __construct($id, $stop_name, $stop_lat, $stop_lon) {
        $this->StopID = (int)$id;
        $this->Name = $stop_name;
        $this->Lat = $stop_lat;
        $this->Lon = $stop_lon;
    }
    function __toString() { //may or may not use $this
        return '<tr><td>' . $this->StopID . '</td><td>' . $this->Name . '</td></tr>';
    }
    function makeMarker () {
        $markString = "var marker" . $this->StopID . " = new google.maps.Marker({
            position: new google.maps.LatLng(" . $this->Lat . "," . $this->Lon . "),
            map: map // by default   
        });
        google.maps.event.addListener(marker" . $this->StopID . ", 'click', function() {
            infoWindow" . $this->StopID . ".open(map, marker" . $this->StopID . ");
        });
        
        var infoWindow" . $this->StopID . " = new google.maps.InfoWindow ({
            content: '" . $this->Name . "'
        });
        ";
        return $markString;
    }
    
}