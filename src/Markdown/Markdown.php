<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Markdown;

use Parsedown;

final class Markdown
{
    private $parser;

    public function __construct($parser = null)
    {
        $this->parser = $parser ?: new Parsedown();
    }

    public function parse(string $text): string
    {
        return $this->parser->text($text);
    }
}
