<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SmartDataObjects\Collection;
use SmartDataObjects\Validators\Anything;

class CollectionTest extends TestCase
{
	public function testReturnsRequiredPropertiesCorrectly()
	{
		$stub = $this->createMock(Collection::class);
		$stub->setRequiredPropertyDefinitions(['required' => null]);

		$this->assertEquals(['required' => null], $stub->getRequiredPropertyDefinitions());

		return $stub;
	}

	/**
	 * @depends testReturnsRequiredPropertiesCorrectly
	 */
	public function testReturnsRequireOnePropertiesCorrectly($stub)
	{
		$stub->setRequireOnePropertyDefinitions(['requiredOne' => null]);

		$this->assertEquals(['requiredOne' => null], $stub->getRequireOnePropertyDefinitions());

		return $stub;
	}

	/**
	 * @depends testReturnsRequireOnePropertiesCorrectly
	 */
	public function testReturnsOptionalPropertiesCorrectly($stub)
	{
		$stub->setOptionalPropertyDefinitions(['optional' => null]);

		$this->assertEquals(['optional' => null], $stub->getOptionalPropertyDefinitions());

		return $stub;
	}

	/**
	 * @depends testReturnsOptionalPropertiesCorrectly
	 */
	public function testReturnsAllPropertiesCorrectly($stub)
	{
		$this->assertEquals(['required' => null, 'requiredOne' => null, 'optional' => null], $stub->getAllPropertyDefinitions());
	}

	public function testCanGetPropertyDefinitionValidators()
	{
		$stub = $this->createMock(Collection::class);
		$stub->setRequiredPropertyDefinitions(['prop' => Anything::class]);
		$this->assertEquals(Anything::class, $stub->getPropertyDefinitionValidator('prop'));

		$stub->setRequiredPropertyDefinitions(['prop' => [Anything::class]]);
		$this->assertEquals(Anything::class, $stub->getPropertyDefinitionValidator('prop'));

		$stub->setRequiredPropertyDefinitions(['prop' => null]);
		$this->assertEquals(null, $stub->getPropertyDefinitionValidator('prop'));
	}

	public function testCanGetPropertyDefinitionOptions()
	{
		$stub = $this->createMock(Collection::class);
		$stub->setRequiredPropertyDefinitions(['prop' => Anything::class]);
		$this->assertEquals([], $stub->getPropertyDefinitionOptions('prop'));

		$stub->setRequiredPropertyDefinitions(['prop' => [Anything::class]]);
		$this->assertEquals([], $stub->getPropertyDefinitionOptions('prop'));

		$stub->setRequiredPropertyDefinitions(['prop' => [Anything::class, []]]);
		$this->assertEquals([], $stub->getPropertyDefinitionOptions('prop'));

		$stub->setRequiredPropertyDefinitions(['prop' => [Anything::class, ['allowNull' => true]]]);
		$this->assertEquals(['allowNull' => true], $stub->getPropertyDefinitionOptions('prop'));

		$this->expectException(\Exception::class);
		$stub->setRequiredPropertyDefinitions(['prop' => [Anything::class, 'SomethingCompletelyWrong']]);
		$stub->getPropertyDefinitionOptions('prop');
	}

	public function testCanSetPropertyValue()
	{
		$stub = $this->createMock(Collection::class);
		$stub->myProperty = null;
		$stub->myValidatedProperty = null;
		$stub->setOptionalPropertyDefinitions([
			'myProperty' => null,
			'myValidatedProperty' => Anything::class,
		]);
		$stub->set('myProperty', 'Some Value');
		$stub->set('myValidatedProperty', 'Some Validated Value');

		$this->assertEquals('Some Value', $stub->myProperty);
		$this->assertEquals('Some Validated Value', $stub->myValidatedProperty);
	}
}