<?php
declare(strict_types=1);

namespace SmartDataObjects\Validators;

use SmartDataObjects\Contracts\Validates;

class Anything implements Validates
{
	public static function validate($value, $options = []): string
	{
		// Literally... Anything!
		return $value;
	}
}