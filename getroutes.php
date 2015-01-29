<?php
//Show all routes.
require './inc_0700/config_inc.php';
$sql = 'select route_id, route_short_name, route_desc from routes';
$iConn = conn();
$result = mysqli_query($iConn,$sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
if(mysqli_num_rows($result) > 0) {
  echo '<table border="true">
  <tr><th>Route ID</th><th>Route Name</th><th>Route Description</th></tr>';
  while ($row=mysqli_fetch_assoc($result)) {
      echo '<tr><td>' . $row['route_id'] . '</td><td>' . $row['route_short_name'] . '</td><td>' . $row['route_desc'] . '</td></tr>';
  }
  echo '</table>';
}
?>
