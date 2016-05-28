<!DOCTYPE html>
<html>
<head>
	<title>My Page to test ticket booking/buying</title>
</head>
<body>
	<h2>Buying Ticket</h2>
	<p>
		<form method="POST" action="reservation">
			<b>Buy a Ticket</b><br>
			<?php echo csrf_field(); ?>
			Movie name: <input name="name" value=""><br>
			Time: <input name="time" value=""><br>
			Theater: <input name="theaterNum" value=""><br>
			Seat row/num: <input size="3" name="seatRow" value=""><input size="3" name="seatNum" value=""><br> 
			Activity:
			<select name="action">
				<option value="book">Book tickets</option>
				<option value="buy">Buy tickets</option>
			</select>
			<br><button type="submit">Seat Reserve</button>
		</form>
	</p>
	<p>
		<form method="POST" action="reservation">
			<b>Purge Reservation</b><br>
			<?php echo csrf_field(); ?>
			<input type="hidden" value="DELETE" name="_method">
			Movie name: <input name="name" value=""><br>
			Time: <input name="time" value=""><br>
			Theater: <input name="theaterNum" value=""><br>
			<button type="submit">Go</button>
		</form>
	</p>
</body>
</html>