<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: sean
 * Date: 18/08/17
 * Time: 19:44
 */

namespace SeanUk\Silex\Stack;

class Stack
{
    /** @var array $contents */
    private $contents;

    public function __construct()
    {
        $this->contents = [];
    }

    /**
     * @param string $json a json string
     * @return bool success status
     */
    public function push(string $json)
    {
        if (!json_decode($json)) {
            return false;
        }

        // push the json onto the stack
        $this->contents[] = $json;
        return true;
    }

    /**
     * Pop the last entry off the stack and return it
     *
     * @return string|null a JSON string, or null if the stack is empty
     */
    public function pop()
    {
        if (empty($this->contents)) {
            return null;
        }
        return array_pop($this->contents);
    }

    /**
     * empty the stack
     */
    public function flush()
    {
        $this->contents = [];
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->contents);
    }
}