<?php

namespace SeaLife\Brainfuck;

/**
 * Brainfuck Parser
 */
class Parser {
    private $memory             = NULL;
    private $maxIterations      = -1;
    private $lastIterationCount = 0;

    public function __construct ($memory = NULL, $maxIterations = -1) {
        $this->memory        = $memory != NULL ? $memory : new Memory();
        $this->maxIterations = $maxIterations;
    }

    /**
     * @param $code
     * @param $i
     *
     * @return mixed
     * @throws ParseException
     */
    protected function skipLoop ($code, $i) {
        $openLoops = 0;

        for ($a = $i; $a < count($code); $a++) {
            if ($code[$a] == "[")
                $openLoops++;
            if ($code[$a] == "]")
                $openLoops--;

            if ($openLoops == 0) {
                return $a;
            }
        }

        throw new ParseException("Loop ending not found! (Opened in Char:$i but never closed!)");
    }

    /**
     * @param $code
     * @param $i
     *
     * @return mixed
     * @throws ParseException
     */
    protected function startOfLoop ($code, $i) {
        $openLoops = 0;

        for ($a = $i; $a > -1; $a--) {
            if ($code[$a] == "]")
                $openLoops++;
            if ($code[$a] == "[")
                $openLoops--;

            if ($openLoops == 0)
                return $a;
        }

        throw new ParseException("Loop start not found! (Closed in Char:$i but never opened?)");
    }

    /**
     * Parses Brainfuck and 'runs' it saving its result into a Memory object.
     *
     * @param          $code  string to be parsed
     *
     * @param string[] $input to be read if ',' is issued, null if it should really ask for input.
     *
     * @return int
     * @throws ParseException will be thrown on a parse error.
     */
    protected function parse ($code, $input = NULL) {
        $code           = str_split($code);
        $inputIteration = 0;
        $iterations     = 0;
        $inLoop         = FALSE;

        for ($i = 0; $i < count($code); $i++) {
            switch ($code[$i]) {
                case '[':
                    if ($this->getMemory()->read() == 0) {
                        $i      = $this->skipLoop($code, $i);
                        $inLoop = FALSE;
                    } else {
                        $inLoop = TRUE;
                    }

                    break;

                case ']':
                    $i = $this->startOfLoop($code, $i) - 1;
                    break;

                case '+':
                    $this->getMemory()->modify(+1);
                    break;

                case '-':
                    $this->getMemory()->modify(-1);
                    break;

                case '>':
                    $this->getMemory()->move(+1);
                    break;

                case '<':
                    $this->getMemory()->move(-1);
                    break;

                case '.':
                    $this->getMemory()->makeChar();
                    break;

                case ',':
                    if ($input != NULL)
                        $char = $input[$inputIteration]; else $char = readline("(in): ");

                    $inputIteration++;

                    $this->getMemory()->set($this->getMemory()->getMemoryPosition(), ord(str_split($char)[0]));
                    break;
            }

            if ($iterations > $this->maxIterations and $this->maxIterations != -1) {
                throw new ParseException("Iteration limit of {$this->maxIterations} reached! " . $this->getMemory()->dump());
            }

            $iterations++;
        }

        if ($inLoop)
            throw new ParseException("Loop not ended? wtf!");

        return $iterations;
    }

    /**
     * @return Memory
     */
    public function getMemory () {
        return $this->memory;
    }

    /**
     * @param      $code string to be parsed.
     *
     * @param null $readArgs
     *
     * @return Memory containing the result.
     * @throws ParseException
     */
    public function run ($code, $readArgs = NULL) {
        $this->lastIterationCount = $this->parse(str_replace("\n", "", $code), $readArgs);

        return $this->memory;
    }

    /**
     * @param      $file string to be parsed.
     *
     * @param null $readArgs
     *
     * @return null|Memory containing the result, null if the file was found.
     * @throws ParseException
     */
    public function runFile ($file, $readArgs = NULL) {
        if (file_exists($file)) {
            $content = file_get_contents($file);

            $this->lastIterationCount = $this->parse(str_replace("\n", "", $content), $readArgs);

            return $this->memory;
        }

        return NULL;
    }

    public function getLastIterationCount () {
        return $this->lastIterationCount;
    }
}