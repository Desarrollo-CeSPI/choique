<?php use_javascript('/sfUJSPlugin/js/jquery.js') ?>
<?php use_stylesheet('/sfUJSPlugin/test/testsuite.css') ?>
<?php use_javascript('/sfUJSPlugin/test/testrunner.js') ?>

<h1>sfUJSPlugin Test Suite</h1>
Mode: <?php echo ($static ? 'static' : 'dynamic') .' '. link_to('[change]', 'sfUJSTest/index?static='.(1 - $static)) ?>
<div id="main" style="display: none;">
</div>
<div id="test_cases" style="display: none;">  
  <?php echo use_helper('UJS') ?>
  <?php echo UJS_set_inclusion($static) ?>  
  
  <div id="test_case1">failure</div>
  <?php UJS("document.getElementById('test_case1').innerHTML='success';") ?>
  
  <div id="test_case2">failure</div>
  <?php UJS_block() ?>
  document.getElementById('test_case2').innerHTML='success';
  <?php UJS_end_block() ?>

  <div id="test_case3">click me</div>
  <?php UJS_add_behaviour('#test_case3', 'click', "test3 = 'success'") ?>
  
  <div id="test_case4" class="bar">Change my attributes</div>
  <?php UJS_change_attributes('#test_case4', 'name=baz') ?>
  <?php UJS_change_attributes('#test_case4', 'class=foo') ?>
  <?php UJS_change_attributes('#test_case4', 'height=30 width=20') ?>
  
  <div id="test_case5">Change my attributes</div>
  <?php UJS_change_attributes('#test_case5', array('height' => 30, 'width' => 20)) ?>
  <?php UJS_change_attributes('#test_case5', 'style=color:yellow') ?>

  <div id="test_case6" style="color:red">change my style</div>
  <?php UJS_change_style('#test_case6', 'text-decoration:underline') ?>
  <?php UJS_change_style('#test_case6', 'color:green') ?>
  <?php UJS_change_style('#test_case6', 'text-align:right font-style:italic') ?>
  <?php UJS_change_style('#test_case6', 'line-height:2px;letter-spacing:2px') ?>
  <?php UJS_change_style('#test_case6', array('font-weight' => '800', 'font-size' => '19px')) ?>

  <?php echo UJS_placeholder('test_case7') ?>
  
  <div id="test_case8"><div id="test_case8_inside">failure</div></div>
  <?php UJS_replace('#test_case8_inside', '<b>success</b>') ?>

  <div id="test_case9"><div id="test_case9_inside">failure</div></div>
  <?php UJS_replace('#test_case9_inside', '<span class=\'foo\' name="bar">success</span>') ?>
  
  <div id="test_case10"><?php UJS_write('success') ?></div>
  
  <div id="test_case11"><?php UJS_write('<b>success</b>') ?></div>
  
  <div id="test_case12"><?php UJS_write_block() ?><b>success</b><?php UJS_end_write_block() ?></div>
  
</div>

<script>
testSuite = function(){
  module("UJS helpers");
  
  test("Basic unobtrusive scripting", function() {
    expect(2);
	  is($('#test_case1').text(), "success", "Scripts added by UJS() are executed" );
	  is($('#test_case2').text(), "success", "Scripts added by UJS_block() are executed" );
	});

  test("Changing DOM element attributes and style", function() {
    expect(11);
	  test3 = 'failure';
	  $('#test_case3').click()
	  is(test3, "success", "UJS_add_behaviour() adds a behaviour to selected nodes" );
	  is($('#test_case4').attr('name'), "baz", "UJS_change_attributes() adds new attributes" );
	  is($('#test_case4').attr('class'), "foo", "UJS_change_attributes() changes existing attributes" );
	  is_deeply([$('#test_case4').attr('width'), $('#test_case4').attr('height')], ["20", "30"], "UJS_change_attributes() can change more than one attribute at a time (string syntax, blank separator)" );
	  is_deeply([$('#test_case5').attr('width'), $('#test_case5').attr('height')], ["20", "30"], "UJS_change_attributes() can change more than one attribute at a time (array syntax)" );
	  is($('#test_case5').css('color'), "yellow", "UJS_change_attributes() can also change the style attribute" );
	  is($('#test_case6').css('text-decoration'), "underline", "UJS_change_style() adds new style attributes" );
	  is($('#test_case6').css('color'), "green", "UJS_change_style() changes existing style attributes" );
	  is_deeply([$('#test_case6').css('text-align'), $('#test_case6').css('font-style')], ["right", "italic"], "UJS_change_style() can change more than one style attribute at a time (string syntax, blank separator)" );
	  is_deeply([$('#test_case6').css('line-height'), $('#test_case6').css('letter-spacing')], ["2px", "2px"], "UJS_change_style() can change more than one style attribute at a time (string syntax, semicolon separator)" );
	  is_deeply([$('#test_case6').css('font-weight'), $('#test_case6').css('font-size')], ["800", "19px"], "UJS_change_style() can change more than one style attribute at a time (array syntax)" );
  });
  
  test("Placeholder, replacer and writer", function() {
    expect(11);
	  ok(String('<?php echo UJS_incremental_id() ?>').indexOf('UJS') === 0 , "UJS_incremental_id() returns a unique id starting with 'UJS'" );
	  ok('<?php echo UJS_incremental_id() ?>' != '<?php echo UJS_incremental_id() ?>', "Each call to UJS_incremental_id() returns a new id" );
	  ok($('#test_case7').is("span"), "UJS_placeholder() inserts a <span> node with the given id" );
	  is($('#test_case7').attr('class'), "UJS_placeholder", "UJS_placeholder() inserts a placeholder with class 'UJS_placeholder'" );
	  is($('#test_case7').css('display'), "none", "UJS_placeholder() inserts an invisible placeholder" );
	  is($('#test_case8').html(), "<b>success</b>", "UJS_replace() replaces existing elements matching selector with given HTML" );
	  is_deeply($('#test_case8_inside'), [], "UJS_replace() removes existing elements matching selector " );
	  is($('#test_case9').html(), '<span class="foo" name="bar">success</span>', "UJS_replace() espaces single and double quotes in second argument properly" );
	  is($('#test_case10').html(), "success", "UJS_write() writes into the document" );
	  is($('#test_case11').html(), "<b>success</b>", "UJS_write() writes HTML into the document" );
	  is($('#test_case12').html(), "<b>success</b>", "UJS_write_block() writes HTML into the document" );
	});
  
  runTest();
};
setTimeout("testSuite();", 500);
</script>


<ol id="tests"></ol>