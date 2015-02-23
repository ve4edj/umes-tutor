<?php 
	require_once("db-settings.php");
	require_once("error_functions.php");

	class Faculty {
		public $id;
		public $name;
		public $code;

		public function __construct($id, $name, $code) {
			$this->id = $id;
			$this->name = $name;
			$this->code = $code;
		}

		public static function getAll() {
    		try {
        		$db = pdoConnect();
				$content = array();
				$query = "SELECT id,name,short_code FROM faculties";
				$statement = $db->prepare($query);
				if (!$statement->execute()) { return false; }
				while($item = $statement->fetch(PDO::FETCH_ASSOC)) {
					$content[$item["id"]] = new Faculty($item["id"],$item["name"],$item["short_code"]);
				}

				$statement = null;
				return $content;
			} catch (PDOException $e) {
      			addAlert("danger", "Oops, looks like our database encountered an error.");
      			error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      			return false;
    		} catch (ErrorException $e) {
      			addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      			return false;
    		}
		}
	}
?>