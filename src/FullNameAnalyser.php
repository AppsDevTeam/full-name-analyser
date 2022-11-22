<?php

declare(strict_types=1);

namespace ADT\FullNameAnalyser;

use ADT\FullNameAnalyser\Exception\InvalidFullNameException;
use ADT\Utils\Strings;
use Exception;
use Granam\CzechVocative\CzechName;
use Transliterator;

final class FullNameAnalyser
{
	const GENDER_MALE = 'male';
	const GENDER_FEMALE = 'female';

	const VOCATIVE_FORM_INFORMAL = 0;
	const VOCATIVE_FORM_FORMAL = 1;

	const DB_MALE = __DIR__ . '/assets/male_first_name.php';
	const DB_MALE_ASCII = __DIR__ . '/assets/male_first_name_ascii.php';
	const DB_FEMALE = __DIR__ . '/assets/female_first_name.php';
	const DB_FEMALE_ASCII = __DIR__ . '/assets/female_first_name_ascii.php';

	private static Transliterator $transliterator;

	private string $language;
	private int $vocativeForm;

	/**
	 * @throws Exception
	 */
	public function __construct(string $language, string $country, int $vocativeForm)
	{
		if (!in_array($language, ['cs', 'sk'])) {
			throw new Exception('Unsupported language.');
		}

		if (!in_array($country, ['CZ', 'SK'])) {
			throw new Exception('Unsupported country.');
		}

		if (!in_array($vocativeForm, [self::VOCATIVE_FORM_INFORMAL, self::VOCATIVE_FORM_FORMAL])) {
			throw new Exception('Unsupported form.');
		}

		$this->language = $language;
		$this->vocativeForm = $vocativeForm;

		self::$transliterator = Transliterator::create('Any-Latin; Latin-ASCII');
	}

	/**
	 * @throws InvalidFullNameException
	 * @throws Exception
	 */
	public function analyse(string $fullName, ?string $gender = null): ?Result
	{
		if (!Strings::validateFullName($fullName)) {
			throw new InvalidFullNameException('Parameter "$fullName" does not seem to be a valid full name.');
		}

		// if only lower letters are used, upper case first letter in each word
		if (mb_strtolower($fullName) === $fullName) {
			$fullName = mb_convert_case($fullName, MB_CASE_TITLE);
		}

		return $this->doAnalyse($fullName, $gender, $fullName !== self::$transliterator->transliterate($fullName));
	}

	/**
	 * @throws Exception
	 */
	private function doAnalyse(string $fullName, ?string $gender, bool $strict): ?Result
	{
		// titul za
		$parts = explode(',', $fullName);
		$titlesAfter = [];
		if (isset($parts[1])) {
			$titlesAfter = explode(" ", trim($parts[1]));
		}

		// titul pred
		$parts = explode(' ', $parts[0]);
		$titlesBefore = [];
		$firstNames = [];
		$lastNames = [];
		foreach ($parts as $_part) {
			if (strstr($_part, '.') !== false) {
				$titlesBefore[] = array_shift($parts);
			}
		}

		// pokud je po odstraneni titulu jen jedno slovo, s nejvetsi pravdepodobnosti je to prijmeni
		if ($titlesBefore && count($parts) === 1) {
			$lastNames[] = $parts[0];

			if (!$gender) {
				$gender = $this->getGender(null, $parts[0], $strict);
			}
		} else {
			// nebyl zadan titul nebo zbyva vice nez jedno slovo
			foreach ($parts as $_part) {
				if (
					$this->isFirstName($_part, $strict) // jedná se o křestní jméno
					&&
					(count($parts) !== 1 || $lastNames) // nezbývá poslední slovo nebo již bylo přiřazeno příjmení
				) {
					$firstNames[] = array_shift($parts);
				} else {
					$lastNames[] = array_shift($parts);
				}
			}

			if (!$firstNames) {
				return null;
			}

			if (!$gender) {
				$gender = $this->getGender($firstNames[0] ?? null, $lastNames[0], $strict);
			}
		}

		$vocative = [];

		if ($this->vocativeForm === self::VOCATIVE_FORM_FORMAL && $_vocativeByTitle = $this->getVocativeByTitle($titlesBefore, $titlesAfter, $gender)) {
			$vocative[] = $_vocativeByTitle;
		} else {
			if ($this->language === 'cs') {
				if ($this->vocativeForm === self::VOCATIVE_FORM_INFORMAL && $firstNames) {
					foreach ($firstNames as $_firstName) {
						$vocative[] = $this->getVocative($_firstName, $gender, false);
					}
				} elseif ($this->vocativeForm === self::VOCATIVE_FORM_FORMAL)  {
					foreach ($lastNames as $_lastName) {
						$vocative[] = $this->getVocative($_lastName, $gender, true);
					}
				}
			} else {
				if ($this->vocativeForm === self::VOCATIVE_FORM_INFORMAL && $firstNames) {
					foreach ($firstNames as $_firstName) {
						$vocative[] = $_firstName;
					}
				} elseif ($this->vocativeForm === self::VOCATIVE_FORM_FORMAL) {
					foreach ($lastNames as $_lastName) {
						$vocative[] = $_lastName;
					}
				}
			}
		}

		return new Result(
			implode(' ', $titlesBefore),
			implode(' ', $firstNames),
			implode(' ', $lastNames),
			implode(' ', $titlesAfter),
			$gender,
			implode(' ', $vocative)
		);
	}

	/**
	 * @throws Exception
	 */
	private function isFirstName(string $name, bool $strict): bool
	{
		return $this->isInDb($name, $strict);
	}

	private function getGender(?string $firstName, string $lastName, $strict): ?string
	{
		// končí-li příjmení na "á" nebo "ova", s největší pravděpodobností to bude žena
		if (
			$this->endsWith($lastName, 'á')
			||
			$this->endsWith($lastName, 'ova')
		) {
			return self::GENDER_FEMALE;
		}

		// pokud není vyplněno křestní jméno a příjmení nekončí na "ová" nebo "ova", považujeme za muže
		if (!$firstName) {
			return self::GENDER_MALE;
		}

		if ($this->isInDb($firstName, $strict, true)) {
			return self::GENDER_MALE;
		} else {
			return self::GENDER_FEMALE;
		}
	}

	private function getVocativeByTitle(array $titlesBefore, array $titlesAfter, string $gender): ?string
	{
		if ($titlesBefore && $titlesBefore[0] === 'prof.') {
			return $this->language === 'cs'
				? ($gender === self::GENDER_MALE ? 'profesore' : 'profesorko')
				: ($gender === self::GENDER_MALE ? 'profesor' : 'profesorka');
		} elseif ($titlesBefore && $titlesBefore[0] === 'doc.') {
			return $this->language === 'cs'
				? ($gender === self::GENDER_MALE ? 'docente' : 'docentko')
				: ($gender === self::GENDER_MALE ? 'docent' : 'docentka');
		} elseif (
			$titlesBefore && in_array($titlesBefore[0], ['Dr.', 'MUDr.', 'MDDr.', 'MVDr.', 'JUDr.', 'RNDr.', 'PharmDr.', 'PhDr.', 'ThDr.', 'ThLic.'])
			||
			$titlesAfter && in_array($titlesAfter[0], ['Ph.D.', 'DrSc.', 'Th.D.', 'DSc.'])
		) {
			return $this->language === 'cs'
				? ($gender === self::GENDER_MALE ? 'doktore' : 'doktorko')
				: ($gender === self::GENDER_MALE ? 'doktor' : 'doktorka');
		} elseif ($titlesBefore && $titlesBefore[0] === 'Ing.') {
			return $this->language === 'cs'
				? ($gender === self::GENDER_MALE ? 'inženýre' : 'inženýrko')
				: ($gender === self::GENDER_MALE ? 'inžinier' : 'inžinierka');
		} elseif ($titlesBefore && $titlesBefore[0] === 'Mgr.') {
			return $this->language === 'cs'
				? ($gender === self::GENDER_MALE ? 'magistře' : 'magistro')
				: ($gender === self::GENDER_MALE ? 'magister' : 'magisterka');
		}

		return null;
	}

	/**
	 * @throws Exception
	 */
	private function getVocative(string $name, string $gender, bool $isLastName): string
	{
		return (new CzechName())->vocative($name, $gender ? $gender === self::GENDER_FEMALE: null, $isLastName);
	}

	private function isInDb(string $name, bool $strict, bool $onlyMale = false): bool
	{
		$name = mb_strtolower($name);
		if ($strict) {
			$dbs = [self::GENDER_MALE => self::DB_MALE, self::GENDER_FEMALE => self::DB_FEMALE];
		} else {
			$dbs = [self::GENDER_MALE => self::DB_MALE_ASCII, self::GENDER_FEMALE => self::DB_FEMALE_ASCII];
		}

		if ($onlyMale) {
			unset ($dbs[self::GENDER_FEMALE]);
		}

		foreach ($dbs as $_db) {
			$loadDb = require $_db;
			if (isset($loadDb[$name])) {
				return true;
			}
		}

		return false;
	}

	private function endsWith(string $haystack, string $needle): bool
	{
		return mb_substr($haystack, -mb_strlen($needle)) === $needle;
	}
}