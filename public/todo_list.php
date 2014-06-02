<?=

        $filename = 'newlist.txt';
        
        //reading file to initialize list, as well as reading uploaded files
        function read_file($givenFile) {
            if (is_readable($givenFile) && filesize($givenFile) > 0){
            $filename = $givenFile;
            // $handle is pointer to file
            $handle = fopen($givenFile, 'r');
            // $contents is the actual list, contained in $givenFile, as a string
            $contents = fread($handle, filesize($givenFile));
            $contents = trim($contents);
            // exploding $contents into array $list
            $list = explode("\n", $contents);
            // closing file
            fclose($handle);
            }
            // returning the $list array
            return $list;
        }

        //saving list array to file
        function save_file($filename = 'newlist.txt', $todo_list) {
            //taking $todo_list array and saving to $filename
            //$filecontents is what will be written to file
            if (is_writable($filename)){
            $filecontents = implode(PHP_EOL, $todo_list);
            //opening $filename
            $handle = fopen($filename, 'w');
            fwrite($handle, $filecontents);
            fclose($handle);
            }
        }    
        // uploading file
        if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0){
            if ($_FILES['file1']['type'] == 'text/plain'){
                $upload_dir = 'vagrant/sites/todo.dev/public/uploads/';
                $filename = basename($_FILES['file1']['name']);
                $saved_filename = $upload_dir . $filename;
                move_uploaded_file($filename, $saved_filename);
            }
        }
        
        
        
        $todo_list = read_file($filename);

        // removing item from list when link is clicked
        if (isset($_GET['list_item'])) {
            $return_index = $_GET['list_item'];
            unset($todo_list[$return_index]);
            save_file($filename, $todo_list);
        }
        
        // adding item to list if form is not empty
        if (!empty($_POST['list_item'])) {
            array_push($todo_list, $_POST['list_item']);
            save_file($filename, $todo_list);
        }

?>
<!DOCTYPE html>
<html>
    <head>
        <title>TODO List</title>
    </head>
    <body>
        <h2>TODO List</h2>
        
        <ul>
            <? foreach ($todo_list as $index => $items): ?>
                    <li><?= htmlspecialchars(strip_tags($items)) ?><a href="todo_list.php?remove=$index"<?=$index?>>Remove Item</a> </li>
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
            save_file('file1', $todo_list);
        ?>
    </body>
</html>