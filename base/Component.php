<?php

namespace wpf\base;

use IteratorAggregate;
use Generator;

/**
 * Class Component
 * @package wpf\base
 */
class Component
    implements IConfigurable, IteratorAggregate
{
    /**
     * @var array
     */
    private array $_attributes = [];

    /**
     * Component constructor.
     *
     * @param array $conf
     */
    public function __construct(array $conf = [])
    {
        $this->loadAttributes($conf);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->_attributes[$name] ?? NULL;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name)
    {
        return (bool)$this->$name;
    }

    /**
     * @param string $name
     */
    public function __unset(string $name)
    {
        if (isset($this->_attributes[$name])) {
            unset($this->_attributes[$name]);
        }
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->_attributes;
    }

    /**
     * @param array $atts
     * @return void
     */
    public function loadAttributes(array $atts): void
    {
        foreach ($atts as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * @return Generator
     */
    public function getIterator(): Generator
    {
        foreach ($this->_attributes as $name => $value) {
            yield $name => $value;
        }
    }
}