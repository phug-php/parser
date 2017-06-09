<?php

namespace Phug;

use Phug\Util\ModuleInterface;

interface ParserModuleInterface extends ModuleInterface
{
    public function injectParser(Parser $parser);
}
