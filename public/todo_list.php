<!DOCTYPE html>


<html>
    <head>
        <title>TODO List</title>
    </head>
    <body>
                <!-- Change the file extension of your todo list template to .php.

        Create an array from your sample todo list items in the template. 
        Next, use PHP to display the array items within the unordered list 
        in your template and test in your browser.

        Reference the code you wrote in your command line todo list app to add 
        the ability to load todo items from a file. The items should be loaded 
        into an array, and then that array should be used to display the items 
        just as in the above steps.

        Using the POST method on the form in your template, create the ability 
        to add todo items to the list. Each time an item is added, the todo list 
        file should be saved with the new item added.

        Add a link next to each todo item that says "Mark Complete" and have it send 
        a GET request to the page that deletes the entry. Use query strings to send 
        the proper key back to the server, and update the todo list file to reflect the deletion.



         -->
    <h2>TODO List</h2>
    <?php
        $todo_list = [];
        foreach ($todo_list as $items){
            echo $items;
            echo "<br>";
        }
    ?>

    <?php
        function read_file() {
            $filename = 'newlist.txt';
            // $handle is pointer to file
            $handle = fopen($filename, 'r');
            $contents = fread($handle, filesize($filename));
            $list = explode("\n", $contents);
            fclose($handle);
            return $list;
        }
        $new_contents = read_file();
        $todo_list = array_merge($todo_list, $new_contents);
        $todo_list = implode('<br>', $todo_list);
        echo $todo_list;
    ?>
    
    
        <!-- <ul>
        	<li>Walk the dog</li>
        	<li>Wash the car</li>
        	<li>Make dinner</li>
        	<li>Buy flowers</li>
        </ul> -->
    <h2> Enter new list item below:</h2>
    <br>
    <?php
        $new_item = $_POST;
        var_dump($new_item);
        $todo_list = explode('<br>', $todo_list);
        $todo_list = array_push($todo_list, $new_item);
        echo $todo_list;
    ?>
    <form method='POST'>
    	<label for="list_item"></label>
    	<input id="list_item" name="list_item" type="text" placeholder="New List Item">
    	<br>
    	<input type="submit"></input>
    </form>
    </body>
</html>