<?php

// M - data model
$continent_id = null;
$continent_name = '';

$countries = array();

// C - controllers
function logic() {
	validate_input();
	load_data();
	render_result();
}

function validate_input() {
	global $continent_id;
	if (!empty($_GET) && isset($_GET['continent_id'])) {
		$tmp_id = $_GET['continent_id'];
		if (ctype_digit($tmp_id)) {
			$continent_id = intval($tmp_id);
			return;
		}
		error_render('continent_id has to be integer.');
	}
	error_render('Need input continent_id in GET parameter.');
}

function load_data() {
	global $continent_id;
	if (is_null($continent_id)) {
		error_render('continent_id has not been set successfully.');
	}

	$link = mysql_connect('localhost', 'mysqldemo', 'mysqlpass');
	if (!$link) {
		error_render('Can not connect - ' . mysql_error());
	}
	$db_selected = mysql_select_db('demo', $link);
	if (!$db_selected) {
		error_render('Can not select - ' . mysql_error());
	}

	$query = sprintf("SELECT cn.id, cn.name, ct.id as country_id, ct.name as country_name, ct.date_format, ct.currency_character
					  FROM continents cn
					      JOIN countries ct ON cn.id = ct.continent_id
					  WHERE cn.id = %d", mysql_real_escape_string($continent_id));
	$result = mysql_query($query);
	if (!$result) {
		error_render('Can not run query - ' . mysql_error());
	}
	global $continent_name, $countries;
	$country_ids = array();
	while ($row = mysql_fetch_assoc($result)) {
		if ($continent_name === '') {
			$continent_name = $row['name'];
		}
		$countries[$row['country_id']] = array('id' => $row['country_id'],
							                   'name' => $row['country_name'],
							                   'date_format' => $row['date_format'],
							                   'currency_character' => $row['currency_character']);
		$country_ids[] = $row['country_id'];
	}

	if (!empty($country_ids)) {
		$query = sprintf("SELECT c.id, s.name, s.code
						  FROM countries c
						  	  LEFT JOIN states s ON c.id = s.country_id
						  WHERE c.id IN (%s)", implode(',', $country_ids));
		$result = mysql_query($query);
		if (!$result) {
			error_render('Can not run query - ' . mysql_error());
		}
		while ($row = mysql_fetch_assoc($result)) {
			$countries[$row['id']]['states'][] = array('name' => $row['name'], 'code' => $row['code']);
		}
	}

	mysql_close($link);
}

// V - view rendering / result displaying
function error_render($reason) {
	echo $reason . "\n";
	exit(1);
}

function render_result() {
	global $countries;
	if (empty($countries)) {
		error_render('No country returned from db to display.');
	}

	global $continent_id, $continent_name;
	$document = new DOMDocument();

	// continent element
	$continent = $document->createElement('continent');

	$domAttr = $document->createAttribute('id');
	$domAttr->value = $continent_id;
	$continent->appendChild($domAttr);

	$domAttr = $document->createAttribute('name');
	$domAttr->value = $continent_name;
	$continent->appendChild($domAttr);

	foreach ($countries as $c_id => $c_val) {
		// country element
		$country = $document->createElement('country');

		$domAttr = $document->createAttribute('id');
		$domAttr->value = $c_val['id'];
		$country->appendChild($domAttr);

		$domAttr = $document->createAttribute('name');
		$domAttr->value = $c_val['name'];
		$country->appendChild($domAttr);

		$domAttr = $document->createAttribute('date_format');
		$domAttr->value = $c_val['date_format'];
		$country->appendChild($domAttr);

		$domAttr = $document->createAttribute('currency_character');
		$domAttr->value = $c_val['currency_character'];
		$country->appendChild($domAttr);

		if (isset($c_val['states']) && !empty($c_val['states'])) {
			foreach ($c_val['states'] as $s_val) {
				// state element
				$state = $document->createElement('state');
				$nameElement = $document->createElement('name', $s_val['name']);
				$codeElement = $document->createElement('code', $s_val['code']);
				$state->appendChild($nameElement);
				$state->appendChild($codeElement);
				// end of state element

				$country->appendChild($state);
			}
		}
		// end of country

		$continent->appendChild($country);
	}
	// end of continent
	$document->appendChild($continent);

	$document->preserveWhiteSpace = false;
	$document->formatOutput = true;
	print_r('<pre>');
	print_r(htmlentities($document->saveXML()));
}

logic();