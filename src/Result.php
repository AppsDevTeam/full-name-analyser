<?php

declare(strict_types=1);

namespace ADT\FullNameAnalyser;

class Result
{
	const GENDER_MALE = 'male';
	const GENDER_FEMALE = 'female';

	private ?string $titleBefore;
	private ?string $firstName;
	private string $lastName;
	private ?string $titleAfter;
	private string $gender;
	private ?string $vocative;

	public function __construct(?string $titleBefore, ?string $firstName, string $lastName, ?string $titleAfter, string $gender, ?string $vocative)
	{
		$this->titleBefore = $titleBefore ?: null;
		$this->firstName = $firstName ?: null;
		$this->lastName = $lastName;
		$this->titleAfter = $titleAfter ?: null;
		$this->gender = $gender;
		$this->vocative = $vocative ?: null;
	}

	public function getTitleBefore(): ?string
	{
		return $this->titleBefore ?: null;
	}

	public function getFirstName(): ?string
	{
		return $this->firstName ?: null;
	}

	public function getLastName(): string
	{
		return $this->lastName;
	}

	public function getTitleAfter(): ?string
	{
		return $this->titleAfter ?: null;
	}

	public function getGender(): string
	{
		return $this->gender;
	}

	public function getVocative(): ?string
	{
		return $this->vocative;
	}
}