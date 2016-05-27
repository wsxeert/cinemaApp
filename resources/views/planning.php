<!DOCTYPE html>
<html>
	<head>
		<title>How is the cinema doing?</title>
	</head>

	<body>

		<h2>Manage Movies</h2>
		<p>
			<form method="GET" action="movieall">
				<b>Show all movies!</b>
				<button type='submit'>Go</button>
			</form>
		</p>
		<p>
			<form method="POST" action="movie">
				<b>Add new movie</b><br>
				<?php echo csrf_field(); ?>
				Movie name: <input name="name" value=""><br>
				Duration: <input name="duration" value=""><br>
				<button type="submit">Add Movie</button>
			</form>
		</p>

		<p>
			<form method="POST" action="movie">
				<b>Update existing movie</b><br>
				<?php echo csrf_field(); ?>
				<input type="hidden" value="PUT" name="_method">
				Movie name: <input name="name" value=""><br>
				New movie name: <input name="newName" value=""><br>
				New duration:  <input name="newDuration" value=""><br>
				<button type="submit">Update Movie</button>
			</form>
		</p>

		<p>
			<form method="POST" action="movie">
				<b>Delete existing movie</b><br>
				<?php echo csrf_field(); ?>
				<input type="hidden" value="DELETE" name="_method">
				Movie name: <input name="name" value=""><br>
				<button type="submit">Delete Movie</button>
			</form>
		</p>

		<h2>Manage Schedules</h2>
		<p>
			<form method="GET" action="schedule">
				<b>Check for schedule!</b>
				<button type="submit">Go</button>
			</form>
		</p>
		<p>
			<form method="POST" action="schedule">
				<b>Add a new schedule</b><br>
				<?php echo csrf_field(); ?>
				Movie name: <input name="name" value=""><br>
				Show time: <input name="time" value=""><br>
				Theater: <input name="theater" value=""><br>
				<button type="submit">Add Schedule</button>
			</form>
		</p>

		<p>
			<form method="POST" action="schedule">
				<b>Update existing schedule</b><br>
				<?php echo csrf_field(); ?>
				<input type="hidden" value="PUT" name="_method">
				Movie name: <input name="name" value=""> New movie name: <input name="newName" value=""> <br>
				Show time: <input name="time" value=""> New show time: <input name="newTime" value=""> <br>
				Theater: <input name="theater" value=""> New thearter: <input name="newTheater" value=""><br>
				<button type="submit">Update Schedule</button>
			</form>
		</p>

		<p>
			<form method="POST" action="schedule">
				<b>Delete existing schedule</b><br>
				<?php echo csrf_field(); ?>
				<input type="hidden" value="DELETE" name="_method">
				Movie name: <input name="name" value=""><br>
				Show time: <input name="time" value=""><br>
				Theater: <input name="theater" value=""><br>
				<button type="submit">Delete Schedule</button>
			</form>
		</p>

	</body>

</html>