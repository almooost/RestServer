<?php
/**
 * Class dbController delivers a whole set of addresses
 * (Singleton)
 * @author Sam Alfano
 * @date 07.06.15
 * @version v0.1
 */

  namespace db;

  class dbController
  {
    private static $obj_instance = null;

    private $a_status;

    private $s_username;
    private $s_password;
    private $s_database;
    private $s_host;
    private $s_dns;
    private $i_port;

    private $dbh;

    /**
     * (private) Initialize new dbController
     * @param int|integer $i_port     Default 3306
     */
    private function __construct($h_connection_info)
    {

      $this->s_username = $h_connection_info["username"];
      $this->s_password = $h_connection_info["password"];
      $this->s_database = $h_connection_info["database"];
      $this->s_host     = $h_connection_info["host"];
      $this->i_port = 3306;
      $this->s_dns = "mysql:host=".$this->s_host.";dbname=".$this->s_database.";port=".$this->i_port.";charset=utf8";
      $this->a_status   = array('status'  => 0,
                                'data'    => array(),
                                'id'      => -1,
                                'msg'     => "");

      $this->dbh = new \PDO($this->s_dns, $this->s_username, $this->s_password, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));

    }

    /**
     * (Singleton) create new object if not exists
     * @return dbController 
     */
    public static function getInstance($h_connection_info)
    {
      if(!is_a(self::$obj_instance,"dbController"))
      {
        self::$obj_instance = new self($h_connection_info);
      }
      return self::$obj_instance;
    }

    /**
     * Open the connection to the mysql server
     * @return void Returns nothing
     */
    private function openConnection()
    {
      $this->reset();
      if(!isset($this->dbh))
        $this->dbh = new \PDO($this->s_dns, $this->s_username, $this->s_password, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION)); 

      
    }

    /**
     * Close connection to the mysql server
     * @return void Returns nothing
     */
    private function closeConnection()
    {
      if($this->dbh)
        $this->dbh = null;
    }

    /**
     * Reset Status to default value
     */
    private function reset()
    {
      $this->a_status   = array('status'  => -1,
                                'data'    => array(),
                                'id'      => -1,
                                'msg'     => "");
    }

    /**
     * Fetch results from DB in Assoc form
     * @return array Associative array (key => value)
     */
    public function fetchAssoc($s_query)
    {
      try
      {
        $this->openConnection();
        $obj_stmt = $this->dbh->query($s_query);
        while($h_row = $obj_stmt->fetch(\PDO::FETCH_ASSOC))
        {          
          array_push($this->a_status['data'],$h_row);
        }
        $this->setStatus(1, $this->dbh->lastInsertId(), "Query sucessfully executed.");
      }
      catch(PDOException $e)
      {
        error_log($e->getMessage());
        $this->setStatus(-1, -1, $e->getMessage());
      }
      finally
      {
        $this->closeConnection();
        return $this->a_status;
      }

    }

    /**
     * Fetch results in numeric order
     * @return array Numeric array
     */
    public function fetchNum($s_query)
    {
      try
      {
        $this->openConnection();
        $obj_stmt = $this->dbh->query($s_query);
        $this->a_status['data'] = $obj_stmt->fetch(\PDO::FETCH_NUM);
        $this->setStatus(1, $this->dbh->lastInsertId(), "Query sucessfully executed.");
      }
      catch(PDOException $e)
      {
        $this->setStatus(-1, -1, $e->getMessage());
      }
      finally
      {
        $this->closeConnection();
        return $this->a_status;
      }

    }

    /**
     * Fetch Results in objects
     * @return array Array of Objects ($object->attribute)
     */
    public function fetchObj($s_query)
    {
      try
      {
        $this->openConnection();
        $obj_stmt = $this->dbh->query($s_query);
        $this->a_status['data'] = $obj_stmt->fetch(\PDO::FETCH_OBJ);
        $this->setStatus(1, $this->dbh->lastInsertId(), "Query sucessfully executed.");
      }
      catch(PDOException $e)
      {
        $this->setStatus(-1, -1, $e->getMessage());
      }
      finally
      {
        $this->closeConnection();
        return $this->a_status;
      }

    }

    /**
     * Delete an entry from the database
     * @return 
     */
    public function delete($s_query)
    {
      try
      {
        $this->openConnection();
        $this->a_status['data'] = $this->dbh->exec($s_query);
        $this->setStatus(1, $this->dbh->lastInsertId(), "Query sucessfully executed.");
      }
      catch(PDOException $e)
      {
        $this->setStatus(-1, -1, $e->getMessage());
      }
      finally
      {
        $this->closeConnection();
        return $this->a_status;
      }

    }

    /**
     * Insert a new record to the DB
     * @param string  SQL Query
     * @param string  Expected return status
     */
    public function insert($s_query)
    {
      try
      {
        $this->openConnection();
        $this->a_status['data'] = $this->dbh->exec($s_query);
        $this->setStatus(1, $this->dbh->lastInsertId(), "Query sucessfully executed.");
      }
      catch(PDOException $e)
      {
        $this->setStatus(-1, -1, $e->getMessage());
      }
      finally
      {
        $this->closeConnection();
        return $this->a_status;
      }

    }

    /**
     * Update an existing record
     * @param string  SQL Query
     * @param string  Expected return value
     */
    public function update($s_query)
    {
      try
      {
        $this->openConnection();
        $this->a_status['data'] = $this->dbh->exec($s_query);
        $this->setStatus(1, $this->dbh->lastInsertId(), "Query sucessfully executed.");
      }
      catch(PDOException $e)
      {
        $this->setStatus(-1, -1, $e->getMessage());
      }
      finally
      {
        $this->closeConnection();
        return $this->a_status;
      }

    }

    /**
     * Create a new transaction for multiple statements
     * @param numeric array   [query,query,..]
     * @param string          expected return value
     */
    public function transaction($a_querys)
    {
      try
      {
        $this->openConnection();
        $this->dbh->beginTransaction();
        foreach ($a_querys as $i_key => $s_query) 
        {
          array_push($this->a_status['data'],$this->dbh->exec($s_query));
        }
        $this->setStatus(1, $this->dbh->lastInsertId(), "Query sucessfully executed.");
        $this->dbh->commit();
      }
      catch(PDOException $e)
      {
        $this->dbh->rollback();
        $this->setStatus(-1, -1, $e->getMessage());
      }
      finally
      {
        $this->closeConnection();
        return $this->a_status;
      }

    }

    /**
     * Set status of action
     * @param int     Status Code
     * @param int     Inserted ID
     * @param string  Message
     */
    private function setStatus($i_status, $i_id, $s_msg)
    {
      $this->a_status['status'] = $i_status;
      $this->a_status['id']     = $i_id;
      $this->a_status['msg']    = $s_msg;
      if($i_status === -1)
        $this->a_status['data'] = null;
    }

    /**
     * Get current Status from object
     * @return mixed var
     */
    public function getStatus($s_status = "status")
    {
      return $this->a_status;
    }
  }


?>
