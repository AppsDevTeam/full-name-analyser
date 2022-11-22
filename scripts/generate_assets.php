<?php

$transliterator = Transliterator::create('Any-Latin; Latin-ASCII');

$maleNames = [];
$asciiMaleNames = [];
$femaleNames = [];
$asciiFemaleNames = [];
if (($handle = fopen(__DIR__  . "/crawler/dataset2.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$name = $data[3];

		// for example "Toma  (1)"
		$name = trim(preg_replace("/ [0-9]+$/", "", $name));

		// TOMÁŠ to Tomáš
		$name = mb_convert_case($name, MB_CASE_TITLE);

		// lowercased and converted to ascii for better matching
		$lowercasedName = mb_strtolower($name);

		// remove accents for alternative matching
		$lowercasedAsciiName = $transliterator->transliterate($lowercasedName);

		if (strstr($data[1], 'm') !== false) {
			$maleNames[$lowercasedName] = true;
			$asciiMaleNames[$lowercasedAsciiName] = true;
		}
		if (strstr($data[1], 'f') !== false) {
			$femaleNames[$lowercasedName] = true;
			$asciiFemaleNames[$lowercasedAsciiName] = true;
		}
	}
	fclose($handle);
}

if (($handle = fopen(__DIR__  . "/kalendar-online/female.txt", "r")) !== FALSE) {
	while (($name = fgets($handle)) !== false) {
		$name = trim($name);

		if (in_array($name, [
			'Ota',
			'Ilja',
			'Vojta',
			'Attila',
			'Karim',
			'Luka',
			'Ricardo',
			'Riccardo',
			'Daniele',
			'Mykola',
			'Atila',
			'Csaba',
			'Ilija',
			'Ivica',
			'Mustafa',
			'Géza',
			'Renato',
			'Nicolae',
			'Juda',
			'Honza',
		])) {
			continue;
		}

		$lowercasedName = mb_strtolower($name);

		if (!isset($femaleNames[$lowercasedName])) {
			$femaleNames[$lowercasedName] = true;
			$asciiFemaleNames[$transliterator->transliterate($lowercasedName)] = true;
		}
	}

	fclose($handle);
}

if (($handle = fopen(__DIR__  . "/kalendar-online/male.txt", "r")) !== FALSE) {
	while (($name = fgets($handle)) !== false) {
		$name = trim($name);

		if (in_array($name, [
			'Petr (stol.)',
			'Natálie',
			'Ingeborg',
			'Zoe',
			'Doris',
			'Edith',
			'Waltraud',
			'Eleni',
			'Janis',
			'Agnes',
			'Hildegard',
			'Annelies',
			'Ilse',
			'Niki',
			'Gerlinde',
			'Annette',
			'Anneliese',
			'Esther',
			'Gertrud',
			'Lieselotte',
			'Vasiliki',
			'Dolores',
			'Gertraud',
			'Gudrun',
			'Heidrun',
			'Edit',
			'Sigrid',
			'Waltraut',
			'Chantal',
			'Elke',
			'Erzsébet',
			'Liv',
			'Brit',
		])) {
			continue;
		}

		$lowercasedName = mb_strtolower($name);

		if (!isset($maleNames[$lowercasedName])) {
			$maleNames[$lowercasedName] = true;
			$asciiMaleNames[$transliterator->transliterate($lowercasedName)] = true;
		}
	}

	fclose($handle);
}

foreach (['Žanet'] as $name) {
	$lowercasedName = mb_strtolower($name);

	if (!isset($femaleNames[$lowercasedName])) {
		$femaleNames[$lowercasedName] = true;
		$asciiFemaleNames[$transliterator->transliterate($lowercasedName)] = true;
	}
}

file_put_contents(__DIR__ . '/../src/assets/male_first_name.php', "<?php return " . var_export($maleNames, true) . ';');
file_put_contents(__DIR__ . '/../src/assets/female_first_name.php', "<?php return " . var_export($femaleNames, true) . ';');

file_put_contents(__DIR__ . '/../src/assets/male_first_name_ascii.php', "<?php return " . var_export($asciiMaleNames, true) . ';');
file_put_contents(__DIR__ . '/../src/assets/female_first_name_ascii.php', "<?php return " . var_export($asciiFemaleNames, true) . ';');