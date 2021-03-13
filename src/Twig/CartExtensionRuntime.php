<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class CartExtensionRuntime implements RuntimeExtensionInterface
{
    const MAX_LENGTH = 20;

    /**
     * Truncates the given string.
     *
     * @param string $text the given text
     *
     * @return string the truncated string
     */
    public function minimizeString(string $text): string
    {
        return \strlen($text) > self::MAX_LENGTH ?
            \sprintf('%s...',
                \mb_substr($text, 0, self::MAX_LENGTH)
            ) : $text;
    }
}
