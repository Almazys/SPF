<?php

class Database extends PDO {

    protected static $count; 

	public function __construct() {
		Debug::write("Building database ...", 1);
        self::$count = 0;
        
        if(!in_array($GLOBALS['config']['bdd']['driver'], $this->getAvailableDrivers()))
            Site::error(Site::app_error, "Database driver not found", $GLOBALS['config']['errors']['framework']['100'] . implode(", ", $this->getAvailableDrivers()));


		$dsn=$GLOBALS['config']['bdd']['driver'] . ":host=" . $GLOBALS['config']['bdd']['hostname'] . ";dbname=" . $GLOBALS['config']['bdd']['database'];

        try { // Should be caught through Site.class.php, but still... just in case
            parent::__construct($dsn, $GLOBALS['config']['bdd']['username'], $GLOBALS['config']['bdd']['password']);
            parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if($GLOBALS['config']['bdd']['driver'] == "mysql") {
                $this->query("SET NAMES " . $GLOBALS['config']['bdd']['encoding']);
                self::$count = 0; # SET NAMES doesn't not count
            }
        } catch (PDOException $e) {
            Site::error(Site::app_error, "Database error", 
                ($GLOBALS['config']['security']['displayExplicitErrors']===true ? $e->getMessage() : $GLOBALS['config']['errors']['framework']['503'])
                        );        
        } 
    }

    public function query($_qry) {
    	Debug::write("SQL query : " . $_qry, 2);
        self::$count = self::$count + 1; 
        try { // Should be caught through Site.class.php, but still... just in case
            return parent::query($_qry);
        } catch (PDOException $e) {
            Site::error(Site::app_error, "Database error", 
                ($GLOBALS['config']['security']['displayExplicitErrors']===true ? $e->getMessage() : $GLOBALS['config']['errors']['framework']['503'])
                        );        
        } 
    }

    public function exec($_qry) {
        Debug::write("SQL exec : " . $_qry, 2);
        self::$count = self::$count + 1; 
        try { // Should be caught through Site.class.php, but still... just in case
            return parent::exec($_qry);
        } catch (PDOException $e) {
            Site::error(Site::app_error, "Database error", 
                ($GLOBALS['config']['security']['displayExplicitErrors']===true ? $e->getMessage() : $GLOBALS['config']['errors']['framework']['503'])
                        );        
        } 
    }

    public function prepare($_qry, $_options=NULL) {	
    	Debug::write("SQL prepare : " . $_qry, 2);
        self::$count = self::$count + 1; 
        try { // Should be caught through Site.class.php, but still... just in case
            if($_options===null)
                return parent::prepare($_qry);
            else
                return parent::prepare($_qry, $_options);
        } catch (PDOException $e) {
            Site::error(Site::app_error, "Database error", 
                ($GLOBALS['config']['security']['displayExplicitErrors']===true ? $e->getMessage() : $GLOBALS['config']['errors']['framework']['503'])
                        );
        } 
    }

    public static function getStats() {
        return self::$count;
    }

}

/**
 * Little reminder
 */

/* Simple req */
// foreach($this->db->query("SELECT * FROM users;") as $row) {
//  var_dump($row);
// }

/* secured req */
// $req = $this->db->prepare("SELECT * FROM users where id= :id");
// $req->execute(array(':id' => 1));
// results = $req->fetchAll();
// foreach ($results as $row) {
//  var_dump($row);
// }

/* req "without select" */
//$this->db->exec("DELETE FROM users;");

?>
