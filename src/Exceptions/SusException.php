<?php
namespace Dialect\Sus\Exceptions;
use Exception;
class SusException extends Exception
{
	/**
	 * {@inheritdoc}
	 */
	protected $message = 'An error occurred';
}