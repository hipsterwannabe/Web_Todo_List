<?=

        $filename = "newlist.txt";
        require_once('classes/filestore.php');
        
        $todo = new Filestore($filename);

        $list = $todo->read();

        // uploading file
        if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0){
            if ($_FILES['file1']['type'] == 'text/plain'){
                $upload_dir = 'uploads/';
                $filename = basename($_FILES['file1']['name']);
                $saved_filename = $upload_dir . $filename;
                move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
                $uploaded_list = read_file($saved_filename);
                array_merge($todo, $uploaded_list);
                save_file($filename, $todo);
            }
        }
        
        $list = $todo->read();
        
        
        

        // removing item from list when link is clicked
        if (isset($_GET['remove'])) {
            unset($list[$_GET['remove']]);
            $todo->write($list);
        }
        
      

        // adding item to list if form is not empty
        if (isset($_POST['list_item'])) {
                // error if input item is longer than 240 characters
                if (strlen($_POST['list_item']) > 240) {
                    throw new Exception("Please keep your TODO item to less than 240 characters.");
                }
                if (strlen($_POST['list_item']) == 0) {
                throw new Exception("Please enter a non-blank TODO item");
                }
            array_push($list, $_POST['list_item']);
            $todo->write($list);
            } 
      
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TODO List</title>
        <link rel = "stylesheet" href = "uploads/todo_css.css">
    </head>
    <body>
        <h2>TODO List</h2>
        <ul>
            <? foreach ($list as $index => $items): ?>
                    <li><?= htmlspecialchars(strip_tags($items)) ." ". "<a href=\"?remove=$index\">Remove Item</a>"?></li>
                <? endforeach; ?>
        
        </ul>   
        <br>
        <!-- user inputs new item -->
        <h2> Enter new list item below:</h2>
        <form method='POST' action="/todo_list.php">
        	<label for="list_item"></label>
        	<input id="list_item" name="list_item" type="text" placeholder="New List Item">
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
        <?=
            $list = $todo->write($list);
        ?>
    </body>
</html>