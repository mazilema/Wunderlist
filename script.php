<?php
	/**
	 * Create new database object, then grab the
	 * user input and save it for parsing
	 */
	$db = new WunderDB();

	/**
	* If no task is specified, default to "all tasks" list
	* If a task is specified, split up the input and continue
	*/
	if ( $argv[ 1 ] === '' ) { 
		$task = 'all tasks'; 		
		
	} else { 
		$task = $argv[ 1 ]; 		
	}
	
	$all = explode( ' ', $task );

	/**
	 * List Tasks
	 */	
	if ( $task === 'tasks' ) { // List all tasks that have not been completed in the Inbox list
		$results = $db->query( 'SELECT * from `tasks` WHERE `list_id` = "1"' );
		$inc = 1;
		
		while ( $row = $results->fetchArray() ) {
			echo "¬ " . $row[ 'name' ] . "\r";
			$inc++;
		}
		
	} else if ( $task === 'all tasks' ) { // List all tasks that have not been completed
		$lists = $db->query( 'SELECT id, name FROM `lists`' );

		while ( $list = $lists->fetchArray() ) {
			$tasks = $db->query( 'SELECT name from `tasks` where `list_id` = "' . $list[ 'id' ] . '"' );
			$inc = 1;
			
			echo $list[ 'name' ] . "\r";
			
			while ( $task = $tasks->fetchArray() ) {
				echo "¬ " . $task[ 'name' ] . "\r";
				$inc++;
			}
			
			if ( $inc == 1 ) { 
				echo "¬ No tasks in this list\r"; 				
			}				
		}
		
	} else { // Add the input text to Wunderlist as a new task.		
		$result = $db->query( 'INSERT into `tasks` ( name, list_id, note, done, position, deleted ) VALUES( "' . $task . '", "1", "", 0, 9, 0 )' );
		echo 'Task added: ' . $task;
	}

	$db->close();

	/**
	 * WunderDB Class
	 * Custom class extending the SQLite class for opening and working with an SQLite database
	 */
	class WunderDB extends SQLite3
	{
		/**
		 * Constructor
		 */
	    function __construct()
	    {
	    	// Default location of wunderlist db file
	    	$dbfile = posix_getpwuid( posix_getuid() )[ 'dir' ] . '/Library/Wunderlist/wunderlist.db';

	    	// Check if wunderlist db file exists and is readable
	    	if ( is_readable( $dbfile ) ) {
	    		$this->open( $dbfile );	

	    	} else {
				exit( 'Unable to find local Wunderlist task database' );
			}
	    }
	}
?>