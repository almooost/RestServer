<?php
/**
 * Interface for class which holds methos for prepare a object for reponse
 * @author Sam Alfano
 * @date 15.06.15
 * @version v0.1
 */

namespace interfaces;

interface Response
{

  /**
   * Return Data ordered in an array, ready for convertion
   * @return hash
   */
  public function toArray();

  /**
   * Finalize representing data
   * @return rsponseController Object
   */
  public function finalize($i_status_code);
}
?>