<?php 
	require_once("db-settings.php");
	require_once("error_functions.php");

	class Course {
		public $id;
		public $courseNumber;
		public $faculty;

		public function __construct($id, $courseNumber, $faculty) {
			$this->id = $id;
			$this->courseNumber = $courseNumber;
			$this->faculty = $faculty;
		}

		public function toString($faculties)
		{
			return sprintf("%s%d", $faculties[$this->faculty]->code, $this->courseNumber);
		}

		public static function getAll() {
			return Course::getAllByQuery("SELECT id,code,faculty_id FROM courses");
		}

		public static function getAllByTutor($tutor) {
			return Course::getAllByQuery(sprintf("SELECT id,code,faculty_id FROM courses INNER JOIN tutor_courses ON courses.id = tutor_courses.course_id WHERE tutor_courses.tutor_id = %d", $tutor));
		}

		public static function getAllByQuery($query) {
    		try {
        		$db = pdoConnect();
				$content = array();
				$statement = $db->prepare($query);
				if (!$statement->execute()) { return false; }
				while($item = $statement->fetch(PDO::FETCH_ASSOC)) {
					$content[$item["id"]] = new Course($item["id"],$item["code"],$item["faculty_id"]);
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