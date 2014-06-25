<?php
    //ESTABLISH DB CONNECTION
    // Get new instance of PDO object
    $dbc = new PDO('mysql:host=127.0.0.1;dbname=todo_db', 'greg', 'quiero');

    // Tell PDO to throw exceptions on error
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$query = 'CREATE TABLE todo_list (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    item VARCHAR(200) NOT NULL,
    PRIMARY KEY (id)
    )';

	// $dbc->exec($query);

	//CHECK IF SOMETHING WAS POSTED.
	//Is item being added? -> Add todo!
	//Is item removed? -> Remove it! (NO GET REQUESTS)
	//*Is list being uploaded? -> Add todos!*
	//Use buttons instead of links to delete items.
	$filename = "newlist.txt";
    require_once('classes/filestore.php');
    
    $todo = new Filestore($filename);
    //this needs to be refactored to read from db
    $list = $todo->read();

    class InvalidInputException extends Exception {}

    // adding item to list if form is not empty
    if (isset($_POST['list_item'])) {
            // error if input item is longer than 240 characters
        try {
            if (strlen($_POST['list_item']) > 240) {
                throw new InvalidInputException("Please keep your TODO item to less than 240 characters.");   
            //error if input item is empty
            }
            if (strlen($_POST['list_item']) == 0) {
                throw new Exception("Please enter a non-blank TODO item");
            //add item to list if neither error occurs
            }
            if (!empty($_POST['list_item'])) {
			    $stmt = $dbc->prepare('INSERT INTO todo_list(item) VALUES (:item)'); 
			    $stmt->bindValue(':item', $_POST['list_item'], PDO::PARAM_INT);
                array_push($list, $_POST['list_item']);
                $todo->write($list);
                $stmt->execute();

            }

        } catch (InvalidInputException $e) {
                echo $e->getMessage();
            }

    }

	//QUERY DB FOR TOTAL TODO COUNT.
    function getOffset() {
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    return ($page - 1) * 4;
	  }


	//define which page user is on
	if (!empty($_GET)) {
	    $pageID = $_GET['page'];
	} else {
	    $pageID = 1;
	}
	//DETERMINE PAGINATION VALUES.

	//QUERY FOR TODOS ON CURRENT PAGE.

?>
<!DOCTYPE html>
<html>
    <head>
        <title>TODO List</title>
        <link rel="stylesheet" href="uploads/todo_css.css">
    </head>
    <body>
        <h2>TODO List</h2>
        <ul>
            <? foreach ($list as $items): ?>
                <li><?= htmlspecialchars(strip_tags($items))?>
			<!--!DELETE BUTTON HERE! -->
				<button class="btn-remove" data-todo="<? $items['id']; ?>">Remove</button>
			</li> 
            <? endforeach; ?>
        
        </ul>  
        <form id="removeForm" action="todo_list_db.php" method="post">
		    <input id="removeId" type="hidden" name="remove" value="">
		</form> 
        <br>
        <!-- user inputs new item -->
        <h2> Enter new list item below:</h2>
        <form method='POST' action="todo_list_db.php">
        	<label for="list_item"></label>
        	<input id="list_item" name="list_item" type="text" placeholder="New List Item" autofocus>
        	<br>
        	<input type="submit"></input>
        </form>
        <br>
        <br>
        <form method='POST' enctype='multipart/form-data' action='/todo_list.php'>
            <label for 'file1'>Upload your todo list:</label>
            <input type='file' id='file1' name='file1'>
        <p>
        <input type="submit" value="Upload">
        </p>
        </form>
    </body>
</html>