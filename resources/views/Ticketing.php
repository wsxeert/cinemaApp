<!DOCTYPE html>
<html>
<head>
	<title>My Page to test ticket booking/buying</title>
</head>
<body>
	<h2>Buying Ticket</h2>
	<p>
		<form method="POST" action="buyticket">
			<b>Buy a Ticket</b><br>
			<?php echo csrf_field(); ?>
			Movie name: <input name="name" value=""><br>
			Time: <input name="time" value=""><br>
			Seat: <input name="seat" value=""><br>
			<button type="submit">Add Movie</button>
		</form>
	</p>


</body>
</html>