<?php
class CV_DB
{
	public static function getTestsForEmployees()
	{	
		global $wpdb;
		$query = '';

		$query .= 
		'SELECT employee.persID, employee.vorname as vorname, employee.name as name,
		tests.id as id, tests.persId as persID, DATE_FORMAT(tests.dateTime, "%d.%m.%Y") as datum , 
		DATE_FORMAT(tests.dateTime, "%H:%i")  as zeit, tests.ergebnis as ergebnis, 
		tests.symptom as symptom , 
		DATE_FORMAT(tests.dateExpired, "%d.%m.%Y") as expiredDate, DATE_FORMAT(tests.dateExpired, "%H:%i") as expiredTime 
		FROM 
			' . $wpdb->prefix . 'corona_employee as employee
		RIGHT JOIN 
			' . $wpdb->prefix . 'corona_test_to_employee as tests ON employee.persID = tests.persID
		ORDER BY
			employee.persID, tests.id DESC' ;
		
		$result = $wpdb->get_results($query);
		return $result;
	}

	public static function getEmployees()
	{	
		global $wpdb;
		$query = '';

		$query .= 
		'SELECT employee.persID, employee.vorname, employee.name
		FROM 
			' . $wpdb->prefix . 'corona_employee as employee
		ORDER BY
			employee.persID';
		
		$result = $wpdb->get_results($query);
		return $result;
	}

	public static function insertEmployee($id, $firstname, $lastname)
	{	
		global $wpdb;
		$query = '';

		$query .= 
		'INSERT INTO ' . $wpdb->prefix . 'corona_employee (persID, vorname, name) VALUES (' .$id. ', "' .$firstname. '", "'.$lastname. '")';
			
		$result = $wpdb->get_results($query);
		
		if ($result > 0) {
			return '<div class="success">Der Mitarbeiter '.$firstname.' ' .$lastname. ' wurde erfolgreich gespeichert.</div>';
		}
	}

	public static function insertTestForEmployee($id, $timestamp, $ergebnis, $symptom, $expired)
	{	
		global $wpdb;
		$query = '';

		$query .= 
		'INSERT INTO ' . $wpdb->prefix . 'corona_test_to_employee (persId, dateTime, ergebnis, symptom, dateExpired) VALUES (' .$id. ', "' .$timestamp. '", "'.$ergebnis. '","' .$symptom. '","' .$expired. '")';
			
		$result = $wpdb->get_results($query);
		
		if ($result > 0) {
			return '<div>Der Test für den Mitarbeiter wurde erfolgreich angelegt.</div>';
		}
	}

	public static function getLastTestForEmployee($personId)
	{	
		global $wpdb;
		$query = '';

		$query .= 
		'SELECT test.id as lastTestId, employee.persID as persID, employee.vorname as vorname, employee.name as name,
          test.id as testId, DATE_FORMAT(test.dateTime, "%d.%m.%Y") as datum , 
          DATE_FORMAT(test.dateTime, "%H:%i") as zeit, test.ergebnis as ergebnis, 
          test.symptom as symptom, test.dateExpired as expired, 
          CONCAT(" <i>", DATE_FORMAT(test.dateTime, "%d.%m.%Y"), " - ", DATE_FORMAT(test.dateTime, "%H:%i")," Uhr </i>") as dateTimeFull, 
          CONCAT(DATE_FORMAT(test.dateExpired, "%d.%m.%Y"), " - ", DATE_FORMAT(test.dateExpired, "%H:%i")) as gueltig
		FROM 
			'. $wpdb->prefix . 'corona_employee as employee 
		RIGHT JOIN 
			'. $wpdb->prefix . 'corona_test_to_employee as test ON employee.persID = test.persID 
		WHERE 
			employee.persID = ' .$personId. ' 
		ORDER BY
			test.id DESC';
		
		$result = $wpdb->get_results($query);
		return $result;
	}
}
