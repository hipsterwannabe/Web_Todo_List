<?php

class Filestore {

    public $filename = '';

    public $is_csv = false;

    function __construct($filename = '') 
    {
        // Sets $this->filename
        $this->filename = $filename;

        $type = substr($this->filename, -3);
        if ($type == 'csv'){
            $this->is_csv = true;
        }
    }

    //function to determine if files is txt or csv
    public function read() 
    {
        if ($this->is_csv) {
            //need to return contents
            return $this->read_csv();
        } else {
            return $this->read_lines();
        }
    }

    public function write($array)
    {
        if ($this->is_csv) {
            $this->write_csv($array);
        } else {
            $this->write_lines($array);
        }
    }
    /**
     * Returns array of lines in $this->filename
     */
    private function read_lines()
    {
    	 //if (is_readable($givenFile) && filesize($givenFile) > 0){
            //$filename = $givenFile;
            // $handle is pointer to file
            $handle = fopen($this->filename, 'r');
            // $contents is the actual list, contained in $givenFile, as a string
            //if filesize is greater than 0, write to file, else, return empty array
            if (filesize($this->filename) > 0) {
            $contents = fread($handle, filesize($this->filename));
            $contents = trim($contents);
            // exploding $contents into array $list
            $list = explode("\n", $contents);
            // closing file
            fclose($handle);
            } else {
                $list = [];
            }
            //}
            // returning the $list array
            return $list;
    }

    /**
     * Writes each element in $array to a new line in $this->filename
     */
    private function write_lines($array)
    {
    	//taking $todo_list array and saving to $filename
            //$filecontents is what will be written to file
            var_dump($array);
            if (is_writable($this->filename)){
            $handle = fopen($this->filename, 'w');
            foreach ($array as $items) {
                fwrite($handle, PHP_EOL . $items);
            }
            fclose($handle);
            }
    }

    /**
     * Reads contents of csv $this->filename, returns an array
     */
    private function read_csv()
    {
    	$handle = fopen($this->filename, 'r');
			$address_book = [];
			while (!feof($handle)){
				$row = fgetcsv($handle);
				if (is_array($row)) {
					$address_book[] = $row;
				}
			}
			fclose($handle);
			return $address_book;	
    }

    /**
     * Writes contents of $array to csv $this->filename
     */
    private function write_csv($array)
    {
    	if (is_writeable($this->filename)) {
				$handle = fopen($this->filename, 'w');
				foreach ($array as $fields) {
					fputcsv($handle, $fields);
				}
				fclose($handle);
			}
    }

}