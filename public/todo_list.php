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

    $filename = "newlist.txt";
    require_once('classes/filestore.php');
    
    $todo = new Filestore($filename);

    $list = $todo->read();

    class InvalidInputException extends Exception {}

    // uploading file
    if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0){
        if ($_FILES['file1']['type'] == 'text/plain'){
            $upload_dir = 'uploads/';
            $up_filename = basename($_FILES['file1']['name']);
            $saved_filename = $upload_dir . $up_filename;
            move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
            $upload_list = new Filestore($saved_filename);
            $uploaded_list = $upload_list->read();
            $list = array_merge($list, $uploaded_list);
            $todo->write($list);            
        }
    }
        
        
        
        

    // removing item from list when link is clicked
    if (isset($_GET['remove'])) {
        unset($list[$_GET['remove']]);
        $todo->write($list);
    }
    
  

    // adding item to list if form is not empty
    if (isset($_POST['list_item'])) {
            // error if input item is longer than 240 characters
        try {
            if (strlen($_POST['list_item']) > 240) {
                throw new InvalidInputException("Please keep your TODO item to less than 240 characters.");   
            }
            if (strlen($_POST['list_item']) == 0) {
                throw new Exception("Please enter a non-blank TODO item");
            }
            if (!empty($_POST['list_item'])) {
                array_push($list, $_POST['list_item']);
                $todo->write($list);
            }

        } catch (InvalidInputException $e) {
                echo $e->getMessage();
            }

    }
            
      
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
            <? foreach ($list as $index => $items): ?>
                <li><?= htmlspecialchars(strip_tags($items)) . " " . "<a href=\"?remove=$index\">Remove Item</a>"?></li>
            <? endforeach; ?>
        
        </ul>   
        <br>
        <!-- user inputs new item -->
        <h2> Enter new list item below:</h2>
        <form method='POST' action="/todo_list.php">
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