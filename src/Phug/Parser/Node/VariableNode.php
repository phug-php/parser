<?php

namespace Phug\Parser\Node;

use Phug\Parser\Node;
use Phug\Util\Partial\AttributeTrait;
use Phug\Util\Partial\NameTrait;

class VariableNode extends Node
{
    use NameTrait;
    use AttributeTrait;
}
