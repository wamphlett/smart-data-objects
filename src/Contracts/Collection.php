<?php

namespace SmartDataObjects\Contracts;

interface Collection
{
	public function set(string $propertyName, $value): void;

	public function getAllPropertyDefinitions(): array;

	public function setRequiredPropertyDefinitions(array $properties): void;
	public function getRequiredPropertyDefinitions(): array;

	public function setRequireOnePropertyDefinitions(array $properties): void;
	public function getRequireOnePropertyDefinitions(): array;

	public function setOptionalPropertyDefinitions(array $properties): void;
	public function getOptionalPropertyDefinitions(): array;
}