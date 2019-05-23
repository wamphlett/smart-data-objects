<?php
declare(strict_types=1);

namespace SmartDataObjects\Validators;

use SmartDataObjects\Contracts\Validates;

class Anything implements Validates
{
	/**
	 * This validation class will just let all data through. This should not be used in production - Instead,
	 * NULL should be passed as the validator definition for clarity that there is no validation happening.
	 *
	 * @param $value
	 * @param array $options
	 * @return string
	 */
	public static function validate($value, $options = [])
	{
		return $value;
	}
}