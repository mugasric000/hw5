<?php
/* ---------------------------------------------------------------------------
 * filename    : fr_events.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program displays a list of events (table: fr_events)
 * ---------------------------------------------------------------------------
 */
session_start();
if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit;
}
$sessionid = $_SESSION['fr_person_id'];
include 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
	<link rel="icon" href="cardinal_logo.png" type="image/png" />
</head>

<body style="background-color: pink !important";>
    <div class="container">
		  <?php 
			//gets logo
			functions::logoDisplay2();
		?>
		<div class="row">
			<h3>Customers</h3>
		</div>
		
		<div class="row">
			<p>Each shift is 4 hours.</p>
			<p>
                    <a href="fr_event_create.php" class="btn btn-success">Create an Order</a>
					<a href="fr_assign_create.php" class="btn btn-success">Assign a Driver</a>
					<a href="fr_persons.php" class="btn btn-success">Driver Information</a>
					
                </p>
			<p>
				<?php if($_SESSION['fr_person_title']=='Administrator')
					echo '<a href="fr_event_create.php" class="btn btn-primary">Add Shift</a>';
				?>
				<a href="logout.php" class="btn btn-warning">Logout</a> &nbsp;&nbsp;&nbsp;
				<?php if($_SESSION['fr_person_title']=='Administrator')
					echo '<a href="fr_persons.php">Volunteers</a> &nbsp;';
				?>
				<a href="fr_order.php">Orders</a> &nbsp;
				<?php if($_SESSION['fr_person_title']=='Administrator')
					echo '<a href="fr_assignments.php">AllShifts</a>&nbsp;';
				?>
				<a href="fr_assignments.php?id=<?php echo $sessionid; ?>">MyOrders</a>&nbsp;
			</p>
			
			<table class="table table-striped table-bordered" style="background-color: lightgrey !important">
				<thead>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Address</th>
						<th>Order</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						include 'database.php';
						$pdo = Database::connect();
						$sql = 'SELECT `fr_order`.*, SUM(case when assign_per_id ='. $_SESSION['fr_person_id'] .' then 1 else 0 end) AS sumAssigns, COUNT(`fr_assignments`.assign_event_id) AS countAssigns FROM `fr_order` LEFT OUTER JOIN `fr_assignments` ON (`fr_order`.id=`fr_assignments`.assign_event_id) GROUP BY `fr_order`.id ORDER BY `fr_order`.event_date ASC, `fr_order`.event_time ASC';
						foreach ($pdo->query($sql) as $row) {
							echo '<tr>';
							echo '<td>'. Functions::dayMonthDate($row['order_date']) . '</td>';
							echo '<td>'. Functions::timeAmPm($row['order_time']) . '</td>';
							echo '<td>'. $row['order_address'] . '</td>';
							if ($row['countAssigns']==0)
								echo '<td>'. $row['order_description'] . ' - UNSTAFFED </td>';
							else
								echo '<td>'. $row['order_description'] . ' (' . $row['countAssigns']. ' volunteers)' . '</td>';
							//echo '<td width=250>';
							echo '<td>';
							echo '<a class="btn" href="fr_event_read.php?id='.$row['id'].'">Details</a> &nbsp;';
							if ($_SESSION['fr_person_title']=='Driver' )
								echo '<a class="btn btn-primary" href="fr_event_read.php?id='.$row['id'].'">Driver</a> &nbsp;';
							if ($_SESSION['fr_person_title']=='Customer' )
								echo '<a class="btn btn-primary" href="fr_event_read.php?id='.$row['id'].'">Customer</a> &nbsp;';
							if ($_SESSION['fr_person_title']=='Administrator' )
								echo '<a class="btn btn-success" href="fr_event_update.php?id='.$row['id'].'">Update</a>&nbsp;';
							if ($_SESSION['fr_person_title']=='Administrator' 
								&& $row['countAssigns']==0)
								echo '<a class="btn btn-danger" href="fr_event_delete.php?id='.$row['id'].'">Delete</a>';
							if($row['sumAssigns']==1) 
								echo " &nbsp;&nbsp;Me";
							echo '</td>';
							echo '</tr>';
						}
						Database::disconnect();
					?>
				</tbody>
			</table>
    	</div>
	
    </div> <!-- end div: class="container" -->
	
  </body>
  
</html>