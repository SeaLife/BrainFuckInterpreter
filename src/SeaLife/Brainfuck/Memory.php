<?php

namespace SeaLife\Brainfuck;

/**
 * Class to handle the brainfuck memory iteration properly easy.
 *
 * An instance of this object will be returned by the Parser if parsing was successful.
 */
class Memory {
    protected $memory         = array();
    protected $memoryPosition = 0;
    protected $string         = "";
    protected $bitSize        = 8;

    public function __construct ($bitSize = 8) {
        $this->bitSize = $bitSize;
    }

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
        if ($amount > 0) {
            for ($i = 0; $i < $amount; $i++)
                $this->increase();
        } else {
            for ($i = 0; $i > $amount; $i--)
                $this->decrease();
        }
    }

    public function increase () {
        if (!isset($this->memory[$this->memoryPosition]))
            $this->memory[$this->memoryPosition] = 0;

        if ($this->memory[$this->memoryPosition] == $this->getMaxSize()) {
            $this->memory[$this->memoryPosition] = 0;
        } else {
            $this->memory[$this->memoryPosition] += 1;
        }
    }

    public function decrease () {
        if (!isset($this->memory[$this->memoryPosition]))
            $this->memory[$this->memoryPosition] = 0;

        if ($this->memory[$this->memoryPosition] == 0) {
            $this->memory[$this->memoryPosition] = $this->getMaxSize();
        } else {
            $this->memory[$this->memoryPosition] -= 1;
        }
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

        return "{MemoryDump str='{$this->getString()}', pointer='{$this->memoryPosition}', content='$cc'}";
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

    public function getMaxSize () {
        return pow(2, $this->bitSize) - 1;
    }
}