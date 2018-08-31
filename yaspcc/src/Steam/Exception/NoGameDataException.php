<?php declare(strict_types=1);

namespace Yaspcc\Steam\Exception;

/**
 * Thrown when app has to rely on the dev api due to request limit, and the app was found,
 * but no data was returned because this API is incomplete.
 *
 * Class NoGameDataException
 * @package Yaspcc\Steam\Exception
 */
class NoGameDataException extends \Exception
{
}