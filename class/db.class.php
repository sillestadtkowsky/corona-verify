<?php
class CV_DB
{
	public static function getTestsForEmployees()
	{	
		global $wpdb;
		$query = '';

		$query .= 
		'SELECT employee.persID, employee.vorname as vorname, employee.name as name, 
		employee.status as status,
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
		'SELECT employee.persID, employee.vorname, employee.name, employee.status 
		FROM 
			' . $wpdb->prefix . 'corona_employee as employee
		ORDER BY
			employee.persID';
		
		$result = $wpdb->get_results($query);
		return $result;
	}

	public static function getLastTestForEmployee($personId)
	{	
		global $wpdb;
		$query = '';

		$query .= 
		'SELECT test.id as lastTestId, employee.persID as persID, employee.vorname as vorname, employee.name as name, 
          employee.status as status,
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
