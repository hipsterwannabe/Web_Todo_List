<?php
    // Get new instance of PDO object
    $dbc = new PDO('mysql:host=127.0.0.1;dbname=todo_db', 'greg', 'quiero');

    // Tell PDO to throw exceptions on error
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$query = 'CREATE TABLE todo_list (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    item VARCHAR(200) NOT NULL,
    to_do_by DATE NOT NULL,
    PRIMARY KEY (id)
    )';

	$dbc->exec($query);




?>