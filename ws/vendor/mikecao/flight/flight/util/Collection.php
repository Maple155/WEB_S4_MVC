<?php
/**
 * Flight: An extensible micro-framework.
 *
 * @copyright   Copyright (c) 2011, Mike Cao <mike@mikecao.com>
 * @license     MIT, http://flightphp.com/license
 */

namespace flight\util;

if (!interface_exists('JsonSerializable')) {
    require_once dirname(__FILE__) . '/LegacyJsonSerializable.php';
}

/**
 * The Collection class allows you to access a set of data
 * using both array and object notation.
 */
class Collection implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable {
    /**
     * Collection data.
     *
     * @var array
     */
    private $data;

    /**
     * Constructor.
     *
     * @param array $data Initial data
     */
    public function __construct(array $data = array()) {
        $this->data = $data;
    }

    public function __get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    public function __isset($key) {
        return isset($this->data[$key]);
    }

    public function __unset($key) {
        unset($this->data[$key]);
    }

    // ✅ ✅ ✅ Fix signatures for PHP 8.1+ strict interfaces

    #[\ReturnTypeWillChange]
    public function offsetGet($offset): mixed {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value): void {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset): bool {
        return isset($this->data[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): void {
        unset($this->data[$offset]);
    }

    public function rewind(): void {
        reset($this->data);
    }

    public function current(): mixed {
        return current($this->data);
    }

    public function key(): mixed {
        return key($this->data);
    }

    public function next(): mixed {
        return next($this->data);
    }

    public function valid(): bool {
        $key = key($this->data);
        return ($key !== NULL && $key !== FALSE);
    }

    public function count(): int {
        return sizeof($this->data);
    }

    public function keys(): array {
        return array_keys($this->data);
    }

    public function getData(): array {
        return $this->data;
    }

    public function setData(array $data): void {
        $this->data = $data;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): mixed {
        return $this->data;
    }

    public function clear(): void {
        $this->data = array();
    }
}
