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
	//$filename = "newlist.txt";
    require_once('classes/filestore.php');
    
    //$todo = new Filestore($filename);
    //this needs to be refactored to read from db
    //$list = $todo->read();

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
                // array_push($list, $_POST['list_item']);
                // $todo->write($list);
                $stmt->execute();

            }

        } catch (InvalidInputException $e) {
                echo $e->getMessage();
            }

    }

    // Delete record
    // if post['remove'] is set
    // delete query with value of remove input
    if (isset($_POST['remove'])) {
        $stmt = $dbc->prepare("DELETE FROM todo_list WHERE id = :id");
        $stmt->bindValue(':id', $_POST['remove'], PDO::PARAM_INT);
        $stmt->execute();
    }

	//QUERY DB FOR TOTAL TODO COUNT.
    function getOffset() {
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    return ($page - 1) * 10;
	}


	
	//DETERMINE PAGINATION VALUES.
    //define which page user is on
    if (!empty($_GET)) {
        $pageID = $_GET['page'];
    } else {
        $pageID = 1;
    }

    function getItems($dbc){
    
        if (!empty($_GET)) {
            $pageID = $_GET['page'];
        } else {
            $pageID = 1;
        };
        $pageID = getOffset();
        $stmt = $dbc->prepare('SELECT * FROM todo_list LIMIT :LIMIT OFFSET :OFFSET'); 
        $stmt->bindValue(':LIMIT', 10, PDO::PARAM_INT);
        $stmt->bindValue(':OFFSET', $pageID, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $items;
    }
    $list = getItems($dbc);
    
    //QUERY FOR TODOS ON CURRENT PAGE.
    $count = $dbc->query('SELECT count(*) FROM todo_list')->fetchColumn();
    $offset = ($pageID * 10);
    $numPages = ceil($count / 10);
    $prev = $pageID - 1;
    $next = $pageID + 1;
	
    //moved query here to fetch info after deleting and adding items
    // $list = $dbc->query("SELECT * FROM todo_list")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TODO List</title>
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css" />
        <link rel="stylesheet" href="uploads/todo_css.css">
        <script src="bootstrap/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
        <h2>TODO List</h2>
        <ul>
            <? foreach ($list as $items): ?>
                <li><?=htmlspecialchars(strip_tags($items['item']))?>
			<!--!DELETE BUTTON HERE! -->
				<button class="btn-remove" data-todo="<?= $items['id']; ?>">Remove</button>
			</li> 
            <? endforeach; ?>
        
        </ul>  
        <form id="remove-form" action="todo_list_db.php" method="post">
		    <input id="remove-id" type="hidden" name="remove" value="">
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
        <ul class="pager">
            <?  if ($prev > 0) : ?>
                    <li class="previous"><a href="?page=<?=$prev?>">&larr; Previous</a></li>
            <? endif; ?>
            <? if ($pageID < $numPages) : ?>
                <li class="next"><a href="?page=<?=$next?>">Next &rarr;</a></li>
            <? endif; ?>
        </ul>
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script>

            $('.btn-remove').click(function () {
                var todoId = $(this).data('todo');
                // console.log(todoId);
                if (confirm('Are you sure you want to remove item ' + todoId + '?')) {
                    $('#remove-id').val(todoId);
                    $('#remove-form').submit();
                }
            }); 

        </script>
    </body>
</html>