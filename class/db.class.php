<?php
class CV_DB
{
	public static function getTestsForEmployees()
	{
		global $wpdb;
		$query = '';

		$query .=
			'SELECT employee.persId, employee.firstname as firstname, employee.lastname as lastname,
		tests.id as id, tests.persId as persId, DATE_FORMAT(tests.dateTime, "%d.%m.%Y") as datum , 
		DATE_FORMAT(tests.dateTime, "%H:%i")  as zeit, tests.testresult as testresult, 
		tests.symptom as symptom , 
		DATE_FORMAT(tests.dateExpired, "%d.%m.%Y") as expiredDate, DATE_FORMAT(tests.dateExpired, "%H:%i") as expiredTime 
		FROM 
			' . $wpdb->prefix . 'corona_employee as employee
		RIGHT JOIN 
			' . $wpdb->prefix . 'corona_test_to_employee as tests ON employee.persId = tests.persId
		ORDER BY
			employee.persId, tests.id DESC';

		$result = $wpdb->get_results($query);
		return esc_sql($result);
	}
	
	public static function getTestsForEmployeesArray()
	{
		global $wpdb;
		$query = '';

		$query .=
			'SELECT employee.persId, employee.firstname as firstname, employee.lastname as lastname,
		tests.id as id, tests.persId as persId, DATE_FORMAT(tests.dateTime, "%d.%m.%Y") as datum , 
		DATE_FORMAT(tests.dateTime, "%H:%i")  as zeit, tests.testresult as testresult, 
		tests.symptom as symptom , 
		DATE_FORMAT(tests.dateExpired, "%d.%m.%Y") as expiredDate, DATE_FORMAT(tests.dateExpired, "%H:%i") as expiredTime 
		FROM 
			' . $wpdb->prefix . 'corona_employee as employee
		RIGHT JOIN 
			' . $wpdb->prefix . 'corona_test_to_employee as tests ON employee.persId = tests.persId
		ORDER BY
			employee.persId, tests.id DESC';

		$result = $wpdb->get_results($query,ARRAY_A);
		return esc_sql($result);
	}

	public static function getTestsForEmployeesByIdArray($testId)
	{
		global $wpdb;

		$searchStringVar = implode(",",$testId);

		$query = '';

		$query .=
			'SELECT employee.persId, employee.firstname as firstname, employee.lastname as lastname,
		tests.id as id, tests.persId as persId, DATE_FORMAT(tests.dateTime, "%d.%m.%Y") as datum , 
		DATE_FORMAT(tests.dateTime, "%H:%i")  as zeit, tests.testresult as testresult, 
		tests.symptom as symptom , 
		DATE_FORMAT(tests.dateExpired, "%d.%m.%Y") as expiredDate, DATE_FORMAT(tests.dateExpired, "%H:%i") as expiredTime 
		FROM 
			' . $wpdb->prefix . 'corona_employee as employee
		RIGHT JOIN 
			' . $wpdb->prefix . 'corona_test_to_employee as tests ON employee.persId = tests.persId
		WHERE
			tests.id in (' .$searchStringVar. ')
		ORDER BY
			employee.persId, tests.id DESC';

		$result = $wpdb->get_results($query,ARRAY_A);
		return esc_sql($result);
	}

	public static function deleteTestsForEmployees($id)
	{
		global $wpdb;
		$query = '';

		$query .=
			'DELETE 
		FROM 
			' . $wpdb->prefix . 'corona_test_to_employee 
		WHERE
		' . $wpdb->prefix . 'corona_test_to_employee.id = ' . $id;

		$result = $wpdb->get_results($query);
		return '<div class="success">Der Test wurde erfolgreich geslöscht.</div>';
	}

	public static function getEmployees()
	{
		global $wpdb;
		$query = '';

		$query .=
			'SELECT employee.persId as persId , employee.firstname as firstname, employee.lastname as lastname 
		FROM 
			' . $wpdb->prefix . 'corona_employee as employee
		ORDER BY
			employee.persId';

		$result = $wpdb->get_results($query);
		return $result;
	}

	public static function deleteEmployees($id)
	{
		global $wpdb;
		$query = '';

		$query .=
			'DELETE 
		FROM 
			' . $wpdb->prefix . 'corona_employee 
		WHERE
		' . $wpdb->prefix . 'corona_employee.persId = ' . $id;

		$result = $wpdb->get_results($query);
		return '<div class="success">Der Mitarbeiter wurde erfolgreich geslöscht.</div>';
	}

	public static function getEmployeesArray($do_search)
	{
		global $wpdb;
		$query = '';

		$query .=
			'SELECT employee.persId as persId , employee.firstname as firstname, employee.lastname as lastname 
		FROM 
			' . $wpdb->prefix . 'corona_employee as employee
			' . $do_search. '
		ORDER BY
			employee.persId';

		$result = $wpdb->get_results($query, ARRAY_A);
		return esc_sql($result);
	}

	public static function insertEmployee($id, $firstname, $lastname)
	{
		global $wpdb;
		$query = '';

		$query .=
			'INSERT INTO ' . $wpdb->prefix . 'corona_employee (persId, firstname, lastname) VALUES (' . $id . ', "' . $firstname . '", "' . $lastname . '")';

		$result = $wpdb->get_results($query);

		if ($result > 0) {
			return '<div class="success">Der Mitarbeiter ' . $firstname . ' ' . $lastname . ' wurde erfolgreich gespeichert.</div>';
		}
	}

	public static function insertTestForEmployee($id, $timestamp, $testresult, $symptom, $expired)
	{
		global $wpdb;
		$query = '';

		$query .=
			'INSERT INTO ' . $wpdb->prefix . 'corona_test_to_employee (persId, dateTime, testresult, symptom, dateExpired) VALUES (' . $id . ', "' . $timestamp . '", "' . $testresult . '","' . $symptom . '","' . $expired . '")';

		$result = $wpdb->get_results($query);

		if ($result > 0) {
			return '<div class="success">Der Test für den Mitarbeiter wurde erfolgreich angelegt.</div>';
		}
	}

	public static function getLastTestForEmployee($personId)
	{
		global $wpdb;
		$query = '';

		$query .=
			'SELECT test.id as lastTestId, employee.persId as persId, employee.firstname as firstname, employee.lastname as lastname,
          test.id as testId, DATE_FORMAT(test.dateTime, "%d.%m.%Y") as datum , 
          DATE_FORMAT(test.dateTime, "%H:%i") as zeit, test.testresult as testresult, 
          test.symptom as symptom, test.dateExpired as expired, 
          CONCAT(" <i>", DATE_FORMAT(test.dateTime, "%d.%m.%Y"), " - ", DATE_FORMAT(test.dateTime, "%H:%i")," Uhr </i>") as dateTimeFull, 
          CONCAT(DATE_FORMAT(test.dateExpired, "%d.%m.%Y"), " - ", DATE_FORMAT(test.dateExpired, "%H:%i")) as gueltig
		FROM 
			' . $wpdb->prefix . 'corona_employee as employee 
		RIGHT JOIN 
			' . $wpdb->prefix . 'corona_test_to_employee as test ON employee.persId = test.persId 
		WHERE 
			employee.persId = ' . $personId . ' 
		ORDER BY
			test.id DESC';

		$DEBUGMESSAGE = 'getLastTestForEmployee SQL: ' . $query.  '';
		CV_UTILS::debugCode($DEBUGMESSAGE);

		$result = $wpdb->get_results($query);
		return esc_sql($result);
	}

	public static function getLastTestForEmployeeArray($personId)
	{
		global $wpdb;
		$query = '';

		$query .=
			'SELECT test.id as lastTestId, employee.persId as persId, employee.firstname as firstname, employee.lastname as lastname,
          test.id as testId, DATE_FORMAT(test.dateTime, "%d.%m.%Y") as datum , 
          DATE_FORMAT(test.dateTime, "%H:%i") as zeit, test.testresult as testresult, 
          test.symptom as symptom, test.dateExpired as expired, 
          CONCAT(" <i>", DATE_FORMAT(test.dateTime, "%d.%m.%Y"), " - ", DATE_FORMAT(test.dateTime, "%H:%i")," Uhr </i>") as dateTimeFull, 
          CONCAT(DATE_FORMAT(test.dateExpired, "%d.%m.%Y"), " - ", DATE_FORMAT(test.dateExpired, "%H:%i")) as gueltig
		FROM 
			' . $wpdb->prefix . 'corona_employee as employee 
		RIGHT JOIN 
			' . $wpdb->prefix . 'corona_test_to_employee as test ON employee.persId = test.persId 
		WHERE 
			employee.persId = ' . $personId . ' 
		ORDER BY
			test.id DESC';

		$result = $wpdb->get_results($query);
		return esc_sql($result);
	}
}
