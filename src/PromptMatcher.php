<?php

/**
 * This file is part of graze/telnet-client.
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/telnet-client/blob/master/LICENSE
 * @link https://github.com/graze/telnet-client
 */

namespace Graze\TelnetClient;

class PromptMatcher
{
    /**
     * @var array
     */
    protected $matches = [];

    /**
     * @var string
     */
    protected $responseText = '';

    /**
     * @param string $prompt
     * @param string $subject
     * @param string $lineEnding
     *
     * @return bool
     */
    public function isMatch($prompt, $subject, $lineEnding, $pattern = '%s%s$')
    {
        // cheap line ending check before expensive regex
        if (substr($subject, -1 * strlen($lineEnding)) != $lineEnding) {
            return false;
        }

        $matches = [];
        $pattern = '/' . $pattern . '/';
        $pattern = sprintf($pattern, $prompt, $lineEnding);

        preg_match($pattern, $subject, $matches);

        if (empty($matches)) {
            return false;
        }

        $responseText = $subject;
        // trim line endings
        $trimmable = [&$matches, &$responseText];
        array_walk_recursive($trimmable, function (&$trimee) use ($lineEnding) {
            $trimee = trim($trimee, $lineEnding);
        });

        $this->matches = $matches;
        $this->responseText = $responseText;

        return true;
    }

    /**
     * @return array
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * @return string
     */
    public function getResponseText()
    {
        return $this->responseText;
    }
}
