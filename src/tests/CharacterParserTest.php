<?php

namespace CharacterParser\tests;

include __DIR__ . '/../CharacterParser.php';
use CharacterParser\CharacterParser;
use PHPUnit_Framework_TestCase;

class CharacterParserTest extends PHPUnit_Framework_TestCase {

    /**
     * works out how much depth changes
     */
    public function testDepth1() {
        $parser = new CharacterParser();
        $state = $parser->parse('foo(arg1, arg2, {\n  foo: [a, b\n');
        $this->assertEquals(1, $state->roundDepth);
        $this->assertEquals(1, $state->curlyDepth);
        $this->assertEquals(1, $state->squareDepth);
    }

    public function testDepth2() {
        $parser = new CharacterParser();
        $state = $parser->parse('foo(arg1, arg2, {\n  foo: [a, b\n');
        $state = $parser->parse('    c, d]\n  })', $state);
        $this->assertEquals(0, $state->roundDepth);
        $this->assertEquals(0, $state->curlyDepth);
        $this->assertEquals(0, $state->squareDepth);
    }

    /**
     * finds contents of bracketed expressions
     */
    public function testParseMax1() {
        $parser = new CharacterParser();
        $section = $parser->parseMax('foo="(", bar="}") bing bong');
        $this->assertEquals(0, $section->start);
        $this->assertEquals(16, $section->end);//exclusive end of string
        $this->assertEquals('foo="(", bar="}"', $section->src);
    }
    public function testParseMax2() {
        $parser = new CharacterParser();
        $section = $parser->parseMax('{foo="(", bar="}"} bing bong', 1);
        $this->assertEquals(1, $section->start);
        $this->assertEquals(17, $section->end);//exclusive end of string
        $this->assertEquals('foo="(", bar="}"', $section->src);
    }

    /**
     * finds code up to a custom delimiter
     */
    public function testParseUntil1() {
        $parser = new CharacterParser();
        $section = $parser->parseUntil('foo.bar("%>").baz%> bing bong', '%>');
        $this->assertEquals(0, $section->start);
        $this->assertEquals(17, $section->end);//exclusive end of string
        $this->assertEquals('foo.bar("%>").baz', $section->src);

    }
    public function testParseUntil2() {
        $parser = new CharacterParser();
        $section = $parser->parseUntil('<%foo.bar("%>").baz%> bing bong', '%>', 2);
        $this->assertEquals(2, $section->start);
        $this->assertEquals(19, $section->end);//exclusive end of string
        $this->assertEquals('foo.bar("%>").baz', $section->src);
    }
    /**
     * parses regular expressions
     */
    public function testRegression1() {
        $parser = new CharacterParser();
        $section = $parser->parseMax('foo=/\\//, bar="}") bing bong');
        $this->assertEquals(0, $section->start);
        $this->assertEquals(17, $section->end);//exclusive end of string
        $this->assertEquals('foo=/\\//, bar="}"', $section->src);
    }
    public function testRegression2() {
        $parser = new CharacterParser();
        $section = $parser->parseMax('foo = typeof /\\//, bar="}") bing bong');
        $this->assertEquals(0, $section->start);
       // $this->assertEquals(18, $section->end);//exclusive end of string
        $this->assertEquals('foo = typeof /\\//, bar="}"', $section->src);
    }
/*
var assert = require('better-assert');
var parser = require('../');
var parse = parser;

it('works out how much depth changes', function () {
  var state = parse('foo(arg1, arg2, {\n  foo: [a, b\n');
  assert(state.roundDepth === 1);
  assert(state.curlyDepth === 1);
  assert(state.squareDepth === 1);

  parse('    c, d]\n  })', state);
  assert(state.squareDepth === 0);
  assert(state.curlyDepth === 0);
  assert(state.roundDepth === 0);
});

it('finds contents of bracketed expressions', function () {
  var section = parser.parseMax('foo="(", bar="}") bing bong');
  assert(section.start === 0);
  assert(section.end === 16);//exclusive end of string
  assert(section.src = 'foo="(", bar="}"');

  var section = parser.parseMax('{foo="(", bar="}"} bing bong', {start: 1});
  assert(section.start === 1);
  assert(section.end === 17);//exclusive end of string
  assert(section.src = 'foo="(", bar="}"');
});

it('finds code up to a custom delimiter', function () {
  var section = parser.parseUntil('foo.bar("%>").baz%> bing bong', '%>');
  assert(section.start === 0);
  assert(section.end === 17);//exclusive end of string
  assert(section.src = 'foo.bar("%>").baz');

  var section = parser.parseUntil('<%foo.bar("%>").baz%> bing bong', '%>', {start: 2});
  assert(section.start === 2);
  assert(section.end === 19);//exclusive end of string
  assert(section.src = 'foo.bar("%>").baz');
});

describe('regressions', function () {
  describe('#1', function () {
    it('parses regular expressions', function () {
      var section = parser.parseMax('foo=/\\//g, bar="}") bing bong');
      assert(section.start === 0);
      assert(section.end === 18);//exclusive end of string
      assert(section.src = 'foo=/\\//g, bar="}"');

      var section = parser.parseMax('foo = typeof /\\//g, bar="}") bing bong');
      assert(section.start === 0);
      //assert(section.end === 18);//exclusive end of string
      assert(section.src = 'foo = typeof /\\//g, bar="}"');
    })
  })
}) */
}
 