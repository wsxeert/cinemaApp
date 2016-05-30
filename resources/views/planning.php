<!DOCTYPE html>
<html>
	<head>
		<title>How is the cinema doing?</title>
	</head>

	<body>

		<h2>Manage Movies</h2>
		<p>
			<form method="GET" action="planning/movie">
				<b>Show all movies!</b>
				<button type='submit'>Go</button>
			</form>
		</p>
		<p>
			<form method="GET" action="planning/movie">
				<b>Find movie info!</b><br>
				Movie name: <input name="name" value=""<br>
				<button type='submit'>Go</button>
			</form>
		</p>
		<p>
			<form method="POST" action="planning/movie/create">
				<b>Add new movie</b><br>
				<?php echo csrf_field(); ?>
				Movie name: <input name="name" value=""><br>
				Duration: <input name="duration" value=""><br>
				<button type="submit">Add Movie</button>
			</form>
		</p>

		<p>
			<form method="POST" action="planning/movie/update">
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
			<form method="POST" action="planning/movie/delete">
				<b>Delete existing movie</b><br>
				<?php echo csrf_field(); ?>
				<input type="hidden" value="DELETE" name="_method">
				Movie name: <input name="name" value=""><br>
				<button type="submit">Delete Movie</button>
			</form>
		</p>

		<h2>Manage Theater</h2>
		<p>
			<form method="POST" action="planning/theater/create">
				<b>Create a new theater</b><br>
				<?php echo csrf_field(); ?>
				Theater number: <input name="num" value=""><br>
				Number of rows: <input name="rows" value=""><br>
				Seats per row: <input name="seats" value=""><br>
				<button type="submit">Create Theater</button>
			</form>
		</p>

		<p>
			<form method="POST" action="planning/theater/delete">
				<b>Delete existing theater</b><br>
				<?php echo csrf_field(); ?>
				<input type="hidden" value="DELETE" name="_method">
				Theater num: <input name="num" value=""><br>
				<input id="recursive" type="checkbox" name="recursive" value="yes"><label for="recursive">Check to find schedules that tied to this theater, delete them!</label> <br>
				<button type="submit">Delete Theater</button>
			</form>
		</p>

		<h2>Manage Schedules</h2>
		<p>
			<form method="GET" action="schedule/all">
				<b>Check for schedule!</b>
				<button type="submit">Go</button>
			</form>
		</p>
		<p>
			<form method="POST" action="planning/schedule/create">
				<b>Add a new schedule</b><br>
				<?php echo csrf_field(); ?>
				Movie name: <input name="name" value=""><br>
				Show time: <input name="time" value=""><br>
				Date: <input name="date" value=""><br>
				Theater: <input name="theater" value=""><br>
				<button type="submit">Add Schedule</button>
			</form>
		</p>

		<p>
			<form method="POST" action="planning/schedule/update">
				<b>Update existing schedule</b><br>
				<?php echo csrf_field(); ?>
				<input type="hidden" value="PUT" name="_method">
				Movie name: <input name="name" value=""> New movie name: <input name="newName" value=""> <br>
				Show time: <input name="time" value=""> New show time: <input name="newTime" value=""> <br>
				Date: <input name="date" value=""> New date: <input name="newDate" value=""><br>
				Theater: <input name="theater" value=""> New thearter: <input name="newTheater" value=""><br>
				<button type="submit">Update Schedule</button>
			</form>
		</p>

		<p>
			<form method="POST" action="planning/schedule/delete">
				<b>Delete existing schedule</b><br>
				<?php echo csrf_field(); ?>
				<input type="hidden" value="DELETE" name="_method">
				ID: <input name="_id" value=""><br>
				Movie name: <input name="name" value=""><br>
				Show time: <input name="time" value=""><br>
				Date: <input name="date" value=""><br>
				Theater: <input name="theater" value=""><br>
				<button type="submit">Delete Schedule</button>
			</form>
		</p>
	</body>

</html>