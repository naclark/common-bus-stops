<?php
//Show stops in common between two routes.
require './inc_0700/config_inc.php';
$sql = 'select rs.stop_id, stop_name from route_stop rs inner join stops s on rs.stop_id = s.stop_id where route_id in ("100223","100224") group by rs.stop_id having count(rs.stop_id) > 1';
$iConn = conn();
$result = mysqli_query($iConn,$sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
if(mysqli_num_rows($result) > 0) {
  echo 'Stops in common between 43 and 44: <table border="true">
  <tr><th>Stop ID</th><th>Stop Name</th></tr>';
  while ($row=mysqli_fetch_assoc($result)) {
      echo '<tr><td>' . $row['stop_id'] . '</td><td>' . $row['stop_name'] . '</td></tr>';
  }
  echo '</table>';
}
?>
