<?php

namespace Phug\Parser\Node;

use Phug\Parser\Node;
use Phug\Util\Partial\AssignmentTrait;
use Phug\Util\Partial\AttributeTrait;
use Phug\Util\Partial\NameTrait;

class ElementNode extends Node
{
    use NameTrait;
    use AttributeTrait;
    use AssignmentTrait;
}
