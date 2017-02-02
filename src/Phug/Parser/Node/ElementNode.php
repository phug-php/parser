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

    /**
     * @return string
     */
    public function getAttribute($name)
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getName() === $name) {
                return $attribute->getValue();
            }
        }
    }
}
