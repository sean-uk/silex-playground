<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: sean
 * Date: 18/08/17
 * Time: 19:44
 */

namespace SeanUk\Silex\Stack;

use Symfony\Component\Serializer\Serializer;

class Stack
{
    /** @var array $contents */
    private $contents;

    /** @var Serializer $serializer */
    private $serializer;

    /** @var string $format serialization format */
    private $format;

    /** @var string $path */
    private $path;

    /**
     * Stack constructor.
     * @param string $path the path to use for the stack file
     * @param Serializer $serializer
     * @param string $serializationFormat the name of a format supported by your serializer
     */
    public function __construct(string $path, Serializer $serializer, string $serializationFormat)
    {
        $this->path = $path;
        $this->serializer = $serializer;
        $this->format = $serializationFormat;

        $this->contents = [];
        $this->restore();
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

        $this->persist();
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

        $item = array_pop($this->contents);
        $this->persist();
        return $item;
    }

    /**
     * persist the stack contents
     */
    protected function persist()
    {
        $serialized = $this->serializer->serialize($this->contents, $this->format);
        file_put_contents($this->path, $serialized);
    }

    /**
     * restore stack contents from file
     *
     * @todo validate/sanitise on load
     */
    protected function restore()
    {
        // if the file doesn't exist do nothing
        if (!is_file($this->path)) {
            return;
        }

        $contents = file_get_contents($this->path);
        $data = $this->serializer->decode($contents, $this->format);
        $this->contents = $data;
    }

    /**
     * empty the stack
     */
    public function flush()
    {
        $this->contents = [];
        $this->persist();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->contents);
    }
}