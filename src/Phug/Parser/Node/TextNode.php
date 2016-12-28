<?php

namespace Phug\Parser\Node;

use Phug\Parser\Node;
use Phug\Util\Partial\EscapeTrait;
use Phug\Util\Partial\ValueTrait;

class TextNode extends Node
{
    use ValueTrait;
    use EscapeTrait;

    private $level = null;

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function getLevel()
    {
        return $this->level;
    }
}
