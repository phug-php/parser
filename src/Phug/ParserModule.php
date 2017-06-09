<?php

namespace Phug;

use Phug\Util\AbstractModule;
use Phug\Util\ModulesContainerInterface;

class ParserModule extends AbstractModule implements ParserModuleInterface
{
    public function injectParser(Parser $parser)
    {
        return $parser;
    }

    public function plug(ModulesContainerInterface $parent)
    {
        parent::plug($this->injectParser($parent));
    }
}
