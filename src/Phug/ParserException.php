<?php

namespace Phug;

use Phug\Util\Partial\PugFileLocationTrait;
use Phug\Util\PugFileLocationInterface;

/**
 * Represents an exception that is thrown during the parsing process.
 */
class ParserException extends \Exception implements PugFileLocationInterface
{
    use PugFileLocationTrait;
}
