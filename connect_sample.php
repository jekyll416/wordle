<?php
	
	/* 	
		This webpage uses a MySQL database as its datasource.
		GUILD is set in index.php and is passed as a $_GET parameter by .htaccess magic 
		This file should be copied as connect.php and values substituted below	
	*/
		
	$db = mysqli_connect("hostname","username","password","wordle_".GUILD) or die("Could not connect");