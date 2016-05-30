<!DOCTYPE html>
<html>
<head>
	<title>My Page to test ticket booking/buying</title>
</head>
<body>
	<h2>Buying Ticket</h2>
	<p>
		<form method="POST" action="reservation/reserveSeats">
			<b>Buy seats</b><br>
			<?php echo csrf_field(); ?>
			BookingId: <input name="bookingid" value=""><br>
			Movie name: <input name="name" value=""><br>
			Date: <input name="date" value=""><br>
			Time: <input name="time" value=""><br>
			Theater: <input name="theaterNum" value=""><br>
			Seat row/num: <input name="seats" value=""> Note: if more than 1 seat, separate by using comma (,) <br> 
			Activity:
			<button name="action" value="buy" type="submit">Buy Seats</button>
			<button name="action" value="book" type="submit">Book Seats</button>
			<br>
		</form>
	</p>
	<p>
		<form method="POST" action="reservation/delete">
			<b>Cancel Booking</b><br>
			<?php echo csrf_field(); ?>
			<input type="hidden" value="DELETE" name="_method">
			Booking ID: <input name="bookid" value=""><br>
			<button type="submit">Go</button>
		</form>
	</p>
	<p>
		<form method="POST" action="reservation/delete/theater">
			<b>Purge reservation for the specified schedule</b><br>
			<?php echo csrf_field(); ?>
			<input type="hidden" value="DELETE" name="_method">
			id: <input name="_id" value=""> this will ignore the rest of the inputs<br>
			Movie name: <input name="name" value=""><br>
			Date: <input name="date" value=""><br>
			Time: <input name="time" value=""><br>
			Theater: <input name="theaterNum" value=""><br>
			<button type="submit">Go</button>
		</form>
	</p>

</body>
</html>