<?php
/**
 * Autoload library, initializes all general autoloads
 */

// Nullify any existing autoloads
spl_autoload_register(null, false);

// Specify extensions that may be loaded
spl_autoload_extensions('.class.php, .lib.php');

/**
 * Load classes by its class name
 * @param  String $s_class s_Class name
 * @return loaded s_class | false
 */
function classLoader($s_class)
{
  $s_filename = strtolower($s_class).'.class.php';
  $s_file = '../inc/class/'.str_replace('\\','/',$s_filename);
  if (!file_exists($s_file))
  {
      return false;
  }
  include_once $s_file;
}

/**
 * Load classes by its library name
 * @param  String $s_lib Library name
 * @return loaded s_lib | false
 */
function libLoader($s_lib)
{
  $s_filename = strtolower($s_lib) . '.lib.php';
  $s_file = '../inc/class/'.$s_filename;
  if (!file_exists($s_file))
  {
      return false;
  }
  include_once $s_file;
}

// Register the loader functions
spl_autoload_register('classLoader');
spl_autoload_register('libLoader');

?>