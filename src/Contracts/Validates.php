<?php

namespace SmartDataObjects\Contracts;

interface Validates
{
	public static function validate($value, $options = []);
}