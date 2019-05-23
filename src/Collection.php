<?php

namespace SmartDataObjects;

use SmartDataObjects\Contracts\Collection as iCollection;
use SmartDataObjects\Contracts\Validates;

class Collection implements iCollection
{
	protected $required = null;
	protected $requireOne = null;
	protected $optional = null;

	/**
	 * @throws \Exception
	 */
	final public function __construct()
	{
		$missing = [];
		$properties = $this->getAllPropertyDefinitions();
		foreach ($properties as $propertyName => $v) {
			if (!property_exists($this, $propertyName)) {
				$missing[] = $propertyName;
			}
		}

		if (!empty($missing)) {
			throw new \Exception('Invalid Collection object ' . get_class($this) . '. Class did not set properties for the following property definitions: ' . implode(', ', $missing));
		}

		$this->validateDefinitions($properties);
	}

	/**
	 * @param string $propertyName
	 * @param $value
	 * @throws \Exception
	 */
	final public function set(string $propertyName, $value): void
	{
		/** @var Validates $validator */
		$validator = $this->getPropertyDefinitionValidator($propertyName);
		$options = $this->getPropertyDefinitionOptions($propertyName);

		$this->{$propertyName} = $validator !== null
			? $validator::validate($value, $options)
			: $value;
	}

	/**
	 * @param $propertyName
	 * @return array
	 * @throws \Exception
	 */
	final public function getPropertyDefinition($propertyName): array
	{
		$properties = $this->getAllPropertyDefinitions();
		if (!array_key_exists($propertyName, $properties)) {
			throw new \Exception("Collection Property does not exist: ${propertyName}");
		}

		return [$propertyName => $properties[$propertyName]];
	}

	/**
	 * @param $propertyName
	 * @return null|string
	 * @throws \Exception
	 */
	final public function getPropertyDefinitionValidator($propertyName): ?string
	{
		$definition = $this->getPropertyDefinition($propertyName);

		$validator = is_array($definition[$propertyName])
			? $definition[$propertyName][0]
			: $definition[$propertyName];

		return $validator;
	}

	/**
	 * @param $propertyName
	 * @return array
	 * @throws \Exception
	 */
	final public function getPropertyDefinitionOptions($propertyName): array
	{
		$definition = $this->getPropertyDefinition($propertyName);

		if (!is_array($definition[$propertyName])) {
			return [];
		}

		if ($definition[$propertyName][1] === null) {
			return [];
		}

		if (is_array($definition[$propertyName][1])) {
			return $definition[$propertyName][1];
		}

		throw new \Exception('Invalid Definition Options. Must be null or an array.');
	}

	/**
	 * @return array
	 */
	final public function getAllPropertyDefinitions(): array
	{
		$requiredInputs = $this->getRequiredPropertyDefinitions();
		$requireOneInputs = $this->getRequireOnePropertyDefinitions();
		$optionalInputs = $this->getOptionalPropertyDefinitions();

		return array_merge($requiredInputs, $requireOneInputs, $optionalInputs);
	}

	/**
	 * @param array $properties
	 * @return void
	 * @throws \Exception
	 */
	final public function setRequiredPropertyDefinitions(array $properties): void
	{
		$this->validateDefinitions($properties);
		$this->required = $properties;
	}

	/**
	 * @return array
	 */
	final public function getRequiredPropertyDefinitions(): array
	{
		return $this->required ?? [];
	}

	/**
	 * @param array $properties
	 * @return void
	 * @throws \Exception
	 */
	final public function setRequireOnePropertyDefinitions(array $properties): void
	{
		$this->validateDefinitions($properties);
		$this->requireOne = $properties;
	}

	/**
	 * @return array
	 */
	final public function getRequireOnePropertyDefinitions(): array
	{
		return $this->requireOne ?? [];
	}

	/**
	 * @param array $properties
	 * @return void
	 * @throws \Exception
	 */
	final public function setOptionalPropertyDefinitions(array $properties): void
	{
		$this->validateDefinitions($properties);
		$this->optional = $properties;
	}

	/**
	 * @return array
	 */
	final public function getOptionalPropertyDefinitions(): array
	{
		return $this->optional ?? [];
	}

	/**
	 * @param null|string $validator
	 * @throws \Exception
	 */
	private function validateDefinitionValidator(?string $validator): void
	{
		if ($validator === null) {
			return;
		}

		$interfaces = class_implements($validator);
		if (!isset($interfaces['SmartDataObjects\\Contracts\\Validates'])) {
			throw new \Exception('Invalid validator. Validators must implement the Validates Contract.');
		}
	}

	/**
	 * @param $options
	 * @throws \Exception
	 */
	private function validateDefinitionOptions($options): void
	{
		// Allowed to be null
		if ($options === null) {
			return;
		}

		// Options usually is an array
		if (is_array($options)) {
			return;
		}

		throw new \Exception('Invalid definition options. Must be null or an array');
	}

	/**
	 * @param $definition
	 * @throws \Exception
	 */
	private function validateDefinition($definition): void
	{
		// Allowed to be null
		if ($definition === null) {
			return;
		}

		// If its a string, it must be a validator class
		if (is_string($definition)) {
			$this->validateDefinitionValidator($definition);
			return;
		}

		// Usually an array if it has options
		if (is_array($definition)) {
			$this->validateDefinitionValidator($definition[0]);
			$this->validateDefinitionOptions($definition[1]);
			return;
		}

		throw new \Exception('Invalid definition type!');
	}

	/**
	 * @param array $definitions
	 * @throws \Exception
	 */
	private function validateDefinitions(array $definitions): void
	{
		foreach ($definitions as $definition) {
			$this->validateDefinition($definition);
		}
	}
}