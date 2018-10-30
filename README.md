# Brainfuck Interpreter

This script will interpret brainfuck within PHP.

## Example

```php
$parser = new Parser();

$parser->run("+++++++++ [-]");

// or to run a file

$parser->runFile($file);

// to modify the starting memory (to start with a custom one) do

$memory = new Memory();
$parser = new Parser($memory);

$result = $parser->run("[>[>+>+<<-]>>[<<+>>-]<<<-]");
```

## Specification

| Character |    C equivalent   | Meaning
| --------- | ----------------- | -------
| &gt;      | ++ptr;            | increment the data pointer (to point to the next cell to the right).
| &lt;      | --ptr;            | decrement the data pointer (to point to the next cell to the left).
| +         | ++*ptr;           | increment (increase by one) the byte at the data pointer.
| -         | --*ptr;           | decrement (decrease by one) the byte at the data pointer.
| .         | putchar(*ptr);    | output the byte at the data pointer.
| ,         | *ptr = getchar(); | accept one byte of input, storing its value in the byte at the data pointer.
| [         | while (*ptr) {    | if the byte at the data pointer is zero, then instead of moving the instruction pointer forward to the next command, jump it forward to the command after the matching `]` command.
| ]         | }                 | if the byte at the data pointer is nonzero, then instead of moving the instruction pointer forward to the next command, jump it back to the command after the matching `[` command.


Source: [Wikipedia](https://en.wikipedia.org/wiki/Brainfuck)