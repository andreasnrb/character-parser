character-parser
================

PHP fork of https://github.com/ForbesLindesay/character-parser
# character-parser

Parse JavaScript one character at a time to look for snippets in Templates.  This is not a validator, it's just designed to allow you to have sections of JavaScript delimited by brackets robustly.
[![Build Status](https://travis-ci.org/cyonite/character-parser.png?branch=master)](https://travis-ci.org/cyonite/character-parser)

## Installation

    ---

## Usage

Work out how much depth changes:

```php
$parser = new CharacterParser();
$state = $parser->parse('foo(arg1, arg2, {\n  foo: [a, b\n');
$this->assertEquals(1, $state->roundDepth);
$this->assertEquals(1, $state->curlyDepth);
$this->assertEquals(1, $state->squareDepth);

$state = $parser->parse('    c, d]\n  })', $state);
$this->assertEquals(0, $state->roundDepth);
$this->assertEquals(0, $state->curlyDepth);
$this->assertEquals(0, $state->squareDepth);
```

### Bracketed Expressions

Find all the contents of a bracketed expression:

```php
$parser = new CharacterParser();
$section = $parser->parseMax('foo="(", bar="}") bing bong');
$this->assertEquals(0, $section->start);
$this->assertEquals(16, $section->end);//exclusive end of string
$this->assertEquals('foo="(", bar="}"', $section->src);

$section = $parser->parseMax('{foo="(", bar="}"} bing bong', 1);
$this->assertEquals(1, $section->start);
$this->assertEquals(17, $section->end);//exclusive end of string
$this->assertEquals('foo="(", bar="}"', $section->src);
```

The bracketed expression parsing simply parses up to but excluding the first unmatched closed bracket (`)`, `}`, `]`).  It is clever enough to ignore brackets in comments or strings.


### Custom Delimited Expressions

Find code up to a custom delimiter:

```php
$parser = new CharacterParser();
$section = $parser->parseUntil('foo.bar("%>").baz%> bing bong', '%>');
$this->assertEquals(0, $section->start);
$this->assertEquals(17, $section->end);//exclusive end of string
$this->assertEquals('foo.bar("%>").baz', $section->src);

$section = $parser->parseUntil('<%foo.bar("%>").baz%> bing bong', '%>', 2);
$this->assertEquals(2, $section->start);
$this->assertEquals(19, $section->end);//exclusive end of string
$this->assertEquals('foo.bar("%>").baz', $section->src);
```

Delimiters are ignored if they are inside strings or comments.

## API

### parse(str, state = defaultState(), start:0, end: src.length})

Parse a string starting at the index start, and return the state after parsing that string.

If you want to parse one string in multiple sections you should keep passing the resulting state to the next parse operation.

Returns a `State` object.

### parseMax(src,start: 0)

Parses the source until the first unmatched close bracket (any of `)`, `}`, `]`).  It returns an object with the structure:

```js
{
  start: 0,//index of first character of string
  end: 13,//index of first character after the end of string
  src: 'source string'
}
```

### parseUntil(src, delimiter, start: 0, includeLineComment: false)

Parses the source until the first occurence of `delimiter` which is not in a string or a comment.  If `includeLineComment` is `true`, it will still count if the delimiter occurs in a line comment, but not in a block comment.  It returns an object with the structure:

```js
{
  start: 0,//index of first character of string
  end: 13,//index of first character after the end of string
  src: 'source string'
}
```

### parseChar(character, state = defaultState())

Parses the single character and returns the state.  See `parse` for the structure of the returned state object.  N.B. character must be a single character not a multi character string.

### defaultState()

Get a default starting state.

### isPunctuator(character)

Returns `true` if `character` represents punctuation in JavaScript.

### isKeyword(name)

Returns `true` if `name` is a keyword in JavaScript.

## State

A state is an object with the following properties

```js
{
  lineComment: false, //true if inside a line comment
  blockComment: false, //true if inside a block comment

  singleQuote: false, //true if inside a single quoted string
  doubleQuote: false, //true if inside a double quoted string
  regexp:      false, //true if inside a regular expression
  escaped: false, //true if in a string and the last character was an escape character

  roundDepth: 0, //number of un-closed open `(` brackets
  curlyDepth: 0, //number of un-closed open `{` brackets
  squareDepth: 0 //number of un-closed open `[` brackets
}
```

It also has the following useful methods:

- `.isString()`  returns `true` if the current location is inside a string.
- `.isComment()` returns `true` if the current location is inside a comment.
- `.isNesting()` returns `true` if the current location is anything but at the top level, i.e. with no nesting.

## License

MIT