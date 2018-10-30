<?php

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SeaLife\Brainfuck\Memory;
use SeaLife\Brainfuck\Parser;

class BrainfuckInterpreterTest extends TestCase {
    public function __construct ($name = NULL, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        include_once __DIR__ . "/../src/SeaLife/Brainfuck/Memory.php";
        include_once __DIR__ . "/../src/SeaLife/Brainfuck/ParseException.php";
        include_once __DIR__ . "/../src/SeaLife/Brainfuck/Parser.php";
    }

    public function testMemoryBasicAdd () {
        $memory = new Memory();
        $memory->modify(+1);
        $memory->modify(+1);
        $memory->modify(+1);

        Assert::assertEquals(3, $memory->read());
    }

    public function testMemoryBasicSub () {
        $memory = new Memory();
        $memory->modify(-1);

        Assert::assertEquals(-1, $memory->read());
    }

    public function testMovePointerAfterAdd () {
        $memory = new Memory();
        $memory->modify(+1);
        $memory->move(+1);

        Assert::assertEquals(0, $memory->read());
    }

    public function testReadCharacter () {
        $memory = new Memory();
        $memory->modify(+33); // 33 = Exclamation mark in ASCII
        $memory->makeChar();

        Assert::assertEquals("!", $memory->getString());
    }

    public function testDumpMemory () {
        $memory = new Memory();
        $memory->modify(+33);
        $memory->makeChar();
        $memory->makeChar();

        Assert::assertEquals("{MemoryDump '!!', 33}", $memory->dump());
    }

    public function testRawMemoryOutput () {
        $memory = new Memory();
        $memory->modify(+1);
        $memory->modify(+1);
        $memory->modify(+1);

        $output = $memory->getRawMemoryTable();

        Assert::assertEquals(3, $output[$memory->getMemoryPosition()]);
    }

    public function testSetMemoryTo () {
        $memory = new Memory();
        $memory->set($memory->getMemoryPosition(), 15);

        Assert::assertEquals(15, $memory->read());
    }

    public function testParserRunBasicHelloWorld () {
        $parser = new Parser();
        $memory = $parser->run('++++++++++[>+++++++>++++++++++>+++>+<<<<-]>++.>+.+++++++..+++.>++.<<+++++++++++++++.>.+++.------.--------.>+.>.');

        Assert::assertEquals('Hello World!', $memory->getString());
    }

    /**
     * @throws \SeaLife\Brainfuck\ParseException
     * @expectedException \SeaLife\Brainfuck\ParseException
     */
    public function testParseErrorLoop () {
        $parser = new Parser();
        $parser->run('++++++++++----]');
    }

    /**
     * @throws \SeaLife\Brainfuck\ParseException
     * @expectedException \SeaLife\Brainfuck\ParseException
     */
    public function testParseErrorLoopInLoop () {
        $parser = new Parser();
        $parser->run('+++++++[++[+++----');
    }

    /**
     * @throws \SeaLife\Brainfuck\ParseException
     * @expectedException \SeaLife\Brainfuck\ParseException
     */
    public function testParseErrorLoopNoEnding () {
        $parser = new Parser();
        $parser->run('[++[+++----');
    }

    /**
     * @throws \SeaLife\Brainfuck\ParseException
     */
    public function testRunFile () {
        $parser = new Parser();
        $parser->runFile(__DIR__ . "/test_1.b");

        Assert::assertNotEmpty($parser->getMemory()->getRawMemoryTable());
    }

    /**
     * @throws \SeaLife\Brainfuck\ParseException
     */
    public function testRunFileNotFound () {
        $parser = new Parser();
        $result = $parser->runFile(__DIR__ . "/test_1_nf.b");

        Assert::assertNull($result);
    }

    /**
     * @throws \SeaLife\Brainfuck\ParseException
     */
    public function testRunReadInput () {
        $parser = new Parser();
        $parser->run(',+.', array('!'));

        Assert::assertEquals('"', $parser->getMemory()->getString());
    }

    /**
     * @throws \SeaLife\Brainfuck\ParseException
     */
    public function testGetLastIterationCount () {
        $parser = new Parser();
        $parser->run(',+.', array('!'));

        Assert::assertEquals(3, $parser->getLastIterationCount());
    }

    /**
     * @throws \SeaLife\Brainfuck\ParseException
     * @expectedException \SeaLife\Brainfuck\ParseException
     */
    public function testExceedIterationLimit () {
        $parser = new Parser(NULL, 1000);
        $parser->run('++++[>+++++<-]>[<+++++>-]+<+[>[>+>+<<-]++>>[<<+>>-]>>>[-]++>[-]+>>>+[[-]++++++>>>]<<<[[<++++++++<++>>-]+<.<[>----<-]<]<<[>>>>>[>>>[-]+++++++++<[>-<-]+++++++++>[-[<->-]+[<<<]]<[>+<-]>]<<-]<<-]');
    }
}