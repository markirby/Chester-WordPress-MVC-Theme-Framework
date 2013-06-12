<?php

$path = dirname(__FILE__);

require_once( $path . '/lib/mustache/Autoloader.php');
require_once( $path . '/lib/mustache/Compiler.php');
require_once( $path . '/lib/mustache/Context.php');
require_once( $path . '/lib/mustache/Engine.php');
require_once( $path . '/lib/mustache/HelperCollection.php');
require_once( $path . '/lib/mustache/Loader.php');
require_once( $path . '/lib/mustache/Loader/MutableLoader.php');
require_once( $path . '/lib/mustache/Loader/ArrayLoader.php');
require_once( $path . '/lib/mustache/Loader/FilesystemLoader.php');
require_once( $path . '/lib/mustache/Loader/StringLoader.php');
require_once( $path . '/lib/mustache/Parser.php');
require_once( $path . '/lib/mustache/Template.php');
require_once( $path . '/lib/mustache/Tokenizer.php');

require_once( $path . '/base_controller.php');
require_once( $path . '/admin_controller.php');
require_once( $path . '/wp_core_data_helpers.php');
require_once( $path . '/pattern_primer/pattern_primer_controller.php');

?>
