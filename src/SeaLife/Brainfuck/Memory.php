<?php

namespace SeaLife\Brainfuck;

/**
 * Class to handle the brainfuck memory iteration properly easy.
 *
 * An instance of this object will be returned by the Parser if parsing was successful.
 */
class Memory {
    protected $memory = array();

    protected $memoryPosition = 0;

    protected $string = "";

    public function read () {
        return isset($this->memory[$this->memoryPosition]) ? $this->memory[$this->memoryPosition] : 0;
    }

    public function makeChar () {
        $this->string .= chr($this->memory[$this->memoryPosition]);
    }

    public function move ($amount) {
        $this->memoryPosition += $amount;
    }

    public function modify ($amount) {
        if (!isset($this->memory[$this->memoryPosition]))
            $this->memory[$this->memoryPosition] = 0;

        $this->memory[$this->memoryPosition] += $amount;
    }

    public function set ($position, $value) {
        $this->memory[$position] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getString () {
        return trim($this->string);
    }

    public function dump () {
        $cc = implode(", ", $this->memory);

        return "{MemoryDump '{$this->getString()}', $cc}";
    }

    /**
     * @return array
     */
    public function getRawMemoryTable () {
        return $this->memory;
    }

    /**
     * @return int
     */
    public function getMemoryPosition () {
        return $this->memoryPosition;
    }
}