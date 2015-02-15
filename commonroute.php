<?php
/**
 * commonroute.php is a postback app that has two modes: select two routes or show their stops in common on a Google map.
 *
 * @package RouteMatch
 * @author Nick Clark <n.alexander.clark@gmail.com>
 * @version 1.1 2015/02/15
 * @link http://www.stoneseas.com/routematch
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License ("OSL") v. 3.0
 * @see Route_inc.php
 * @see Stop_inc.php 
 * @todo Center map on one of the stops.
 * @todo Add route direction data.
 * @todo Figure out that include_once dealie.
 */


include_once '../inc_0700/Route_inc.php'; //Not sure why I get errors without include_once.
include '../inc_0700/Stop_inc.php';
// include db credentials, etc.

define('THIS_PAGE', basename($_SERVER['PHP_SELF']));
define('MAP_API_KEY', 'not actually a key');

if (isset($_GET['route1']) && isset($_GET['route2'])) { //Show stops.
    showMap();
} else { //Show routes form.
    showRoutes();
}

function showRoutes()
{//Select routes
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	echo '<h3 align="center">Select two routes!</h3>';
	if (!isset($_SESSION['routeList'])){
        //Fill up the route list.
        $sql = "select route_id, route_short_name, route_desc from routes order by route_short_name";
        $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
        if (mysqli_num_rows($result) > 0) { //This may be redundant.
            while ($row = mysqli_fetch_assoc($result))
            {
                $_SESSION['routeList'][] = new Route($row['route_id'],$row['route_short_name'],$row['route_desc']);
            }
        }
    }
    // Make the two select boxes.
    echo '<form action="'. THIS_PAGE . '" method=GET">
    First route: <select name="route1">';
    foreach ($_SESSION['routeList'] as $route) {
        echo $route;
    }
    echo '</select> Second route: <select name="route2">';
    foreach ($_SESSION['routeList'] as $route) {
        echo $route;
    }
    echo '</select>
    <input type="submit" value="Check routes!" />
    </form>';
}

function showMap()
{
	$iConn = IDB::conn();
	$route1 = (int)$_GET['route1']; // Invalid IDs become 0. Thanks, goofy PHP typing!
    $route2 = (int)$_GET['route2'];
    $stops = array();
    $sql = "select s.stop_id, stop_name, stop_lat, stop_lon from route_stop rs 
            inner join stops s on rs.stop_id = s.stop_id 
            where route_id in ('" . $route1 . "','" . $route2 . "') 
            group by rs.stop_id having count(rs.stop_id) > 1";
    $result = mysqli_query($iConn,$sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
    if(mysqli_num_rows($result) > 0) {
        while ($row=mysqli_fetch_assoc($result)) {
            $stops[] = new Stop($row['stop_id'], $row['stop_name'], $row['stop_lat'], $row['stop_lon']);
        }
        echo '
        <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>
        <script src="//maps.googleapis.com/maps/api/js?key="' . MAP_API_KEY . '" type="text/javascript"></script>
        <script type="text/javascript">
        
        
        $(document).ready(function() {
        //Center map on Seattle.
        var latlng = new google.maps.LatLng(47.605976, -122.334511);
    
        var map = new google.maps.Map(document.getElementById("mapDiv"), { zoom: 13, center: latlng, mapTypeId: google.maps.MapTypeId.ROADMAP });

        // Markers from the stops
        ';
        foreach ($stops as $stop) {
            echo $stop->makeMarker();
        }
        echo '
        });        
        
        </script>
        </head>
        <body>
        <p>Stops in common between your selected routes:</p> 
        <div id="mapDiv" style="width:75%;height:100%;margin:0;padding:0;"></div>
        <table border="true"> 
            <tr><th>Stop ID</th><th>Stop Name</th></tr>';
        foreach ($stops as $row) {
            echo $row;
        }
        echo '</table></body>';
    } else {
        echo '<p>There are no matches between the two routes selected. <a href="'. THIS_PAGE .'">Try again?</a></p>';
    }
}