<?php

namespace Unit\FullNameAnalyser;

use ADT\FullNameAnalyser\FullNameAnalyser;
use ADT\FullNameAnalyser\Exception\InvalidFullNameException;
use ADT\FullNameAnalyser\Result;
use Codeception\AssertThrows;
use Codeception\Test\Unit;
use Exception;
use Throwable;
use UnitTester;

class FullNameAnalyserTest extends Unit
{
	use AssertThrows;

	protected UnitTester $tester;

	public function analyseDataProvider(): array
	{
		return [
			'cs; informal; Tomáš Kudělka' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result(null, 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'Tomáši'),
			],
			'cs; informal; Ing. Tomáš Kudělka' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Ing. Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result('Ing.', 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'Tomáši'),
			],

			'cs; informal; Jana Holečková' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Jana Holečková',
				'gender' => null,
				'expected' => new Result(null, 'Jana', 'Holečková', null, FullNameAnalyser::GENDER_FEMALE, 'Jano'),
			],
			'cs; informal; Ing. Jana Holečková' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Ing. Jana Holečková',
				'gender' => null,
				'expected' => new Result('Ing.', 'Jana', 'Holečková', null, FullNameAnalyser::GENDER_FEMALE, 'Jano'),
			],

			'cs; formal; Tomáš Kudělka' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result(null, 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'Kudělko'),
			],
			'cs; formal; Ing. Tomáš Kudělka' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result('Ing.', 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'inženýre'),
			],
			'cs; formal; Mgr. Tomáš Kudělka' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Mgr. Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result('Mgr.', 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'magistře'),
			],
			'cs; formal; MUDr. Tomáš Kudělka' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'MUDr. Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result('MUDr.', 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'doktore'),
			],
			'cs; formal; Ing. Tomáš Kudělka, Ph.D.' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Tomáš Kudělka, Ph.D.',
				'gender' => null,
				'expected' => new Result('Ing.', 'Tomáš', 'Kudělka', 'Ph.D.', FullNameAnalyser::GENDER_MALE, 'doktore'),
			],
			'cs; formal; doc. Ing. Tomáš Kudělka, Ph.D.' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'doc. Ing. Tomáš Kudělka, Ph.D.',
				'gender' => null,
				'expected' => new Result('doc. Ing.', 'Tomáš', 'Kudělka', 'Ph.D.', FullNameAnalyser::GENDER_MALE, 'docente'),
			],
			'cs; formal; prof. Ing. Tomáš Kudělka, Ph.D.' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'prof. Ing. Tomáš Kudělka, Ph.D.',
				'gender' => null,
				'expected' => new Result('prof. Ing.', 'Tomáš', 'Kudělka', 'Ph.D.', FullNameAnalyser::GENDER_MALE, 'profesore'),
			],

			'cs; formal; Jana Holečková' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Jana Holečková',
				'gender' => null,
				'expected' => new Result(null, 'Jana', 'Holečková', null, FullNameAnalyser::GENDER_FEMALE, 'Holečková'),
			],
			'cs; formal; Ing. Jana Holečková' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Jana Holečková',
				'gender' => null,
				'expected' => new Result('Ing.', 'Jana', 'Holečková', null, FullNameAnalyser::GENDER_FEMALE, 'inženýrko'),
			],
			'cs; formal; Mgr. Jana Holečková' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Mgr. Jana Holečková',
				'gender' => null,
				'expected' => new Result('Mgr.', 'Jana', 'Holečková', null, FullNameAnalyser::GENDER_FEMALE, 'magistro'),
			],
			'cs; formal; MUDr. Jana Holečková, Ph.D.' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Jana Holečková, Ph.D.',
				'gender' => null,
				'expected' => new Result('Ing.', 'Jana', 'Holečková', 'Ph.D.', FullNameAnalyser::GENDER_FEMALE, 'doktorko'),
			],
			'cs; formal; Ing. Jana Holečková, Ph.D.' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Jana Holečková, Ph.D.',
				'gender' => FullNameAnalyser::GENDER_FEMALE,
				'expected' => new Result('Ing.', 'Jana', 'Holečková', 'Ph.D.', FullNameAnalyser::GENDER_FEMALE, 'doktorko'),
			],
			'cs; formal; doc. Ing. Jana Holečková, Ph.D.' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'doc. Ing. Jana Holečková, Ph.D.',
				'gender' => FullNameAnalyser::GENDER_FEMALE,
				'expected' => new Result('doc. Ing.', 'Jana', 'Holečková', 'Ph.D.', FullNameAnalyser::GENDER_FEMALE, 'docentko'),
			],
			'cs; formal; prof. Ing. Jana Holečková, Ph.D.' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'prof. Ing. Jana Holečková, Ph.D.',
				'gender' => FullNameAnalyser::GENDER_FEMALE,
				'expected' => new Result('prof. Ing.', 'Jana', 'Holečková', 'Ph.D.', FullNameAnalyser::GENDER_FEMALE, 'profesorko'),
			],

			'sk; informal; Tomáš Kudělka' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result(null, 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'Tomáš'),
			],
			'sk; informal; Ing. Tomáš Kudělka' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Ing. Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result('Ing.', 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'Tomáš'),
			],

			'sk; informal; Jana Holečková' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Jana Holečková',
				'gender' => null,
				'expected' => new Result(null, 'Jana', 'Holečková', null, FullNameAnalyser::GENDER_FEMALE, 'Jana'),
			],
			'sk; informal; Ing. Jana Holečková' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Ing. Jana Holečková',
				'gender' => null,
				'expected' => new Result('Ing.', 'Jana', 'Holečková', null, FullNameAnalyser::GENDER_FEMALE, 'Jana'),
			],

			'sk; formal; Tomáš Kudělka' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result(null, 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'Kudělka'),
			],
			'sk; formal; Ing. Tomáš Kudělka' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result('Ing.', 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'inžinier'),
			],
			'sk; formal; Mgr. Tomáš Kudělka' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Mgr. Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result('Mgr.', 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'magister'),
			],
			'sk; formal; MUDr. Tomáš Kudělka' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'MUDr. Tomáš Kudělka',
				'gender' => null,
				'expected' => new Result('MUDr.', 'Tomáš', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'doktor'),
			],
			'sk; formal; Ing. Tomáš Kudělka, Ph.D.' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Tomáš Kudělka, Ph.D.',
				'gender' => null,
				'expected' => new Result('Ing.', 'Tomáš', 'Kudělka', 'Ph.D.', FullNameAnalyser::GENDER_MALE, 'doktor'),
			],
			'sk; formal; doc. Ing. Tomáš Kudělka, Ph.D.' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'doc. Ing. Tomáš Kudělka, Ph.D.',
				'gender' => null,
				'expected' => new Result('doc. Ing.', 'Tomáš', 'Kudělka', 'Ph.D.', FullNameAnalyser::GENDER_MALE, 'docent'),
			],
			'sk; formal; prof. Ing. Tomáš Kudělka, Ph.D.' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'prof. Ing. Tomáš Kudělka, Ph.D.',
				'gender' => null,
				'expected' => new Result('prof. Ing.', 'Tomáš', 'Kudělka', 'Ph.D.', FullNameAnalyser::GENDER_MALE, 'profesor'),
			],

			'sk; formal; Jana Holečková' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Jana Holečková',
				'gender' => null,
				'expected' => new Result(null, 'Jana', 'Holečková', null, FullNameAnalyser::GENDER_FEMALE, 'Holečková'),
			],
			'sk; formal; Ing. Jana Holečková' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Jana Holečková',
				'gender' => null,
				'expected' => new Result('Ing.', 'Jana', 'Holečková', null, FullNameAnalyser::GENDER_FEMALE, 'inžinierka'),
			],
			'sk; formal; Mgr. Jana Holečková' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Mgr. Jana Holečková',
				'gender' => null,
				'expected' => new Result('Mgr.', 'Jana', 'Holečková', null, FullNameAnalyser::GENDER_FEMALE, 'magisterka'),
			],
			'sk; formal; MUDr. Jana Holečková, Ph.D.' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Jana Holečková, Ph.D.',
				'gender' => null,
				'expected' => new Result('Ing.', 'Jana', 'Holečková', 'Ph.D.', FullNameAnalyser::GENDER_FEMALE, 'doktorka'),
			],
			'sk; formal; Ing. Jana Holečková, Ph.D.' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Jana Holečková, Ph.D.',
				'gender' => FullNameAnalyser::GENDER_FEMALE,
				'expected' => new Result('Ing.', 'Jana', 'Holečková', 'Ph.D.', FullNameAnalyser::GENDER_FEMALE, 'doktorka'),
			],
			'sk; formal; doc. Ing. Jana Holečková, Ph.D.' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'doc. Ing. Jana Holečková, Ph.D.',
				'gender' => FullNameAnalyser::GENDER_FEMALE,
				'expected' => new Result('doc. Ing.', 'Jana', 'Holečková', 'Ph.D.', FullNameAnalyser::GENDER_FEMALE, 'docentka'),
			],
			'sk; formal; prof. Ing. Jana Holečková, Ph.D.' => [
				'language' => 'sk',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'prof. Ing. Jana Holečková, Ph.D.',
				'gender' => FullNameAnalyser::GENDER_FEMALE,
				'expected' => new Result('prof. Ing.', 'Jana', 'Holečková', 'Ph.D.', FullNameAnalyser::GENDER_FEMALE, 'profesorka'),
			],

			'missing first name; informal; Ing. Petrů; no gender' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Ing. Petrů',
				'gender' => null,
				'expected' => new Result('Ing.', null, 'Petrů', null, FullNameAnalyser::GENDER_MALE, null),
			],
			'missing first name; informal; Ing. Petrů; female' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Ing. Petrů',
				'gender' => FullNameAnalyser::GENDER_FEMALE,
				'expected' => new Result('Ing.', null, 'Petrů', null, FullNameAnalyser::GENDER_FEMALE, null),
			],
			'missing first name; formal; Ing. Petrů; no gender' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Petrů',
				'gender' => null,
				'expected' => new Result('Ing.', null, 'Petrů', null, FullNameAnalyser::GENDER_MALE, 'inženýre'),
			],
			'missing first name; formal; Ing. Petrů; female' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Ing. Petrů',
				'gender' => FullNameAnalyser::GENDER_FEMALE,
				'expected' => new Result('Ing.', null, 'Petrů', null, FullNameAnalyser::GENDER_FEMALE, 'inženýrko'),
			],

			'two first names' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Tomáš Pavel Kudělka',
				'gender' => null,
				'expected' => new Result(null, 'Tomáš Pavel', 'Kudělka', null, FullNameAnalyser::GENDER_MALE, 'Tomáši Pavle'),
			],

			'two last names' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Václav Noid Bárta',
				'gender' => null,
				'expected' => new Result(null, 'Václav', 'Noid Bárta', null, FullNameAnalyser::GENDER_MALE, 'Noide Bárto'),
			],

			'two first names without last name' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Tomáš Pavel',
				'gender' => null,
				'expected' => new Result(null, 'Tomáš', 'Pavel', null, FullNameAnalyser::GENDER_MALE, 'Tomáši'),
			],

			'male name with female gender' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Tomáš Novák',
				'gender' => FullNameAnalyser::GENDER_FEMALE,
				'expected' => new Result(null, 'Tomáš', 'Novák', null, FullNameAnalyser::GENDER_FEMALE, 'Novák'),
			],

			'female name with male gender' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'Petra Nováková',
				'gender' => FullNameAnalyser::GENDER_MALE,
				'expected' => new Result(null, 'Petra', 'Nováková', null, FullNameAnalyser::GENDER_MALE, 'Novákováe'),
			],

			'female name without ová' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'Petra Novák',
				'gender' => null,
				'expected' => new Result(null, 'Petra', 'Novák', null, FullNameAnalyser::GENDER_FEMALE, 'Petro'),
			],

			'name without diacritics' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_FORMAL,
				'fullName' => 'vaclav novak',
				'gender' => null,
				'expected' => new Result(null, 'Vaclav', 'Novak', null, FullNameAnalyser::GENDER_MALE, 'Novaku'),
			],

			'nonexisting name' => [
				'language' => 'cs',
				'form' => FullNameAnalyser::VOCATIVE_FORM_INFORMAL,
				'fullName' => 'AAAAAA BBBBBBB',
				'gender' => null,
				'expected' => null,
			],
		];
	}

	/**
	 * @dataProvider analyseDataProvider
	 * @throws Exception
	 */
	public function testAnalyse(string $language, int $form, string $fullName, ?string $gender, ?Result $expected)
	{
		$this->tester->assertEquals($expected, (new FullNameAnalyser($language, 'CZ', $form))->analyse($fullName, $gender));
	}

	/**
	 * @throws Throwable
	 */
	public function testExceptions()
	{
		$this->assertThrows(Exception::class, function() {
			(new FullNameAnalyser('en', 'CZ', 0));
		});
		$this->assertThrows(Exception::class, function() {
			(new FullNameAnalyser('cs', 'US', 0));
		});
		$this->assertThrows(Exception::class, function() {
			(new FullNameAnalyser('cs', 'CZ', 3));
		});
		$this->assertThrows(InvalidFullNameException::class, function() {
			(new FullNameAnalyser('cs', 'CZ', 0))->analyse('x');
		});
	}

	public function testResult()
	{
		$result = new Result('a', 'b', 'c', 'd', Result::GENDER_MALE, 'e');

		$this->tester->assertEquals('a', $result->getTitleBefore());
		$this->tester->assertEquals('b', $result->getFirstName());
		$this->tester->assertEquals('c', $result->getLastName());
		$this->tester->assertEquals('d', $result->getTitleAfter());
		$this->tester->assertEquals(Result::GENDER_MALE, $result->getGender());
		$this->tester->assertEquals('e', $result->getVocative());
	}
}