<?php
/**
 * Encodes an object to an xml
 * @author Sam Alfano
 * @date 13.06.15
 * @version v0.1
 */
namespace utility;

class XML {

  /**
   * Encode an object as XML string
   *
   * @param Object $obj
   * @param string $s_root_node
   * @return string $s_xml
   */
  public static function encodeObj($obj, $s_root_node = 'response') {
    $s_xml = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
    $s_xml .= self::encode($obj, $s_root_node, $i_depth = 0);
    return $s_xml;
  }


  /**
   * Encode an object as XML string
   *
   * @param Object|array $h_data
   * @param string $s_root_node
   * @param int $i_depth Used for indentation
   * @return string $s_xml
   */
  private static function encode($obj_data, $s_node, $i_depth) {
    $s_xml = str_repeat("\t", $i_depth);
    $s_xml .= "<{$s_node}>" . PHP_EOL;
    foreach($obj_data as $key => $val) {
      if(is_array($val) || is_object($val)) {
        $s_xml .= self::encode($val, $key, ($i_depth + 1));
      } else {
        $s_xml .= str_repeat("\t", ($i_depth + 1));
        $s_xml .= "<{$key}>" . htmlspecialchars($val) . "</{$key}>" . PHP_EOL;
      }
    }
    $s_xml .= str_repeat("\t", $i_depth);
    $s_xml .= "</{$s_node}>" . PHP_EOL;
    return $s_xml;
  }
}