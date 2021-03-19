<?php

namespace Core\Model;

use PDO;
use App\Config;

abstract class BaseModel
{
    /**
     * Get the PDO and mysql database connection
     *
     * @return mixed
     */
    protected function connectDB()
    {
        $conn = null;
        if (getenv('DB_CONNECTION') === "mysql") {
            $conn = mysqli_connect(getenv('DB_HOST'). ":". getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
        } else if (getenv('DB_CONNECTION') === "PDO") {
            $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_USERNAME') . ';charset=utf8';
            $conn = new PDO($dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
            // Throw an Exception when an error occurs
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $conn;
    }
    
    /**
     * Insert data in the database.
     *
     * @param $table string [table on database].
     * @param $args  array  [`["colunm1" => "value", "colunm2" => 'value']`].
     * 
     * @return boolean.
     */
    protected function insert($table, $args)
    {
        try {
            $db = $this->connectDB();
            if ($table === null) {
                throw new Exception('table not exist');
            }
            if (empty($args)) {
                throw new Exception('value insert empty');
            }
            $columns = implode(", ",array_keys($args));
            $escaped_values = array_map(array($db, 'real_escape_string'), array_values($args));
            $values  = "'" . implode("','", $escaped_values) . "'";
            $sql = "INSERT INTO $table ($columns) VALUES ($values)";
            if ($db->query($sql) === true ) {
                $db->close();
                return true;
            } else {
                $db->close();
                throw new \Exception('insert db error: ', $db->error);
            }
        }  catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
    
    /**
     * Insert multiple data in the database.
     *
     * @param $table string [table on database].
     * @param $args  array  [`[["colunm1" => "value", "colunm2" => 'value'], ["colunm1" => "value", "colunm2" => 'value']]`].
     * 
     * @return boolean.
     */
    protected function insertMultiple($table, $args)
    {
        $db = $this->connectDB();
        if ($table === null) {
            throw new \Exception('table not exist');
        }
        if (empty($args)) {
            throw new \Exception('value insert empty');
        }
        $columns = implode(", ",array_keys($args[0]));
        $values = array();
        foreach ($args as $vals) {
            $escaped_values = array_map(array($db, 'real_escape_string'), array_values($vals));
            $values[] = "('" . implode("','", $escaped_values) . "')";
        }
        $valueString = implode(", ", $values);
        $sql = 'INSERT INTO '.$table.' ('.$columns.') VALUES'. $valueString;
        if ($db->query($sql) === true ) {
            $db->close();
            return true;
        } else {
            $db->close();
            throw new \Exception('insert db multiple values error: ', $db->error);
        }
    }
    
    /**
     * Update data in the database.
     *
     * @param $table string  [table on database].
     * @param $args  array   [["colunm1" => "value", "colunm2" => 'value']].
     * @param $id    integer [find record is id and update]
     * 
     * @return boolean.
     */
    protected function update($table, $args, $id)
    {
        try {
            $db = $this->connectDB();
            $setColunm = '';
            foreach ($args as $key => $val) {
                $setColunm .= $key. "='". $val. "', ";
            }
            $sql = 'UPDATE '.$table.' SET '. rtrim($setColunm, ", ") .' WHERE id='.intval($id);
            if ($db->query($sql) === true ) {
                $db->close();
                return true;
            } else {
                $db->close();
                throw new Exception("error:", $db->error);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }
    
    /**
     * Find record in the database.
     *
     * @param $table string  [table name] 
     * @param $id    integer [find record is id and update]
     * 
     * @return array.
     */
    protected function find($table, $id, $args = [])
    {
        $db = $this->connectDB();
        $select = "*";
        if (!empty($args)){
            $escaped_select = array_map(array($db, 'real_escape_string'), array_values($args));
            $select  = implode(", ", $escaped_select);
        }
        $sql = "SELECT $select FROM $table WHERE id=$id";
        $result = mysqli_query($db, $sql);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $data = $row;
        }
        $db->close();
        return $data;
    }
    
     /**
     * Delete record in the database.
     *
     * @param $table string  [table name]
     * @param $id    integer [find record is id and update]
     * 
     * @return boolean.
     */
    protected function delete($table, $id)
    {
        $db = $this->connectDB();
        $sql = "DELETE FROM $table WHERE id=$id";
        if ($db->query($sql) === TRUE) {
            return true;
            $db->close();
        } else {
            throw new \Exception('Error deleting record: ', $db->error);
            $db->close();
        }
    }
    
     /**
     * Get all records in the database.
     *
     * @param $table    string  [table name]
     * @param $column   array   [column select default is *]
     * @param $sortType string  [sort type]
     * @param $sortOrder string [sort order default by DESC]
     * 
     * @return array.
     */
    protected function getAll($table, $columns = [], $sortType = 'created_at', $sortOrder = 'DESC')
    {
        $db = $this->connectDB();
        $columnStr = '*';
        if(!empty($columns)) {
            $columnStr = implode(", ", $columns);
        }
        $sql = "SELECT $columnStr FROM $table ORDER BY $sortType $sortOrder";
        $result = mysqli_query($db, $sql);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)){
                $data[] = $row;
            }
        }
        $db->close();
        return $data;
    }
    
    /**
     * Get total records in the database.
     *
     * @param $table string [table name]
     * 
     * @return integer.
     */
    protected function count($table)
    {
        try {
            $db = $this->connectDB();
            $sql = "SELECT count(id) as total from $table";
            $result = mysqli_query($db, $sql);
            $row = mysqli_fetch_assoc($result);
            $db->close();
            return $row['total'];
        }  catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
    
    /**
     * Get records limit in the database.
     *
     * @param $table   string  [table name]
     * @param $columns array   [column select default is *]
     * @param $skip    integer [skip record default is 0]
     * @param $take    integer [take record defaul is 10]
     * 
     * 
     * @return array.
     */
    protected function limit($table, $columns = [], $skip = 0, $take = 10)
    {
        try {
            $db = $this->connectDB();
            $columnStr = '*';
            if(!empty($columns)) {
                $columnStr = implode(", ", $columns);
            }
            $sql = "SELECT $columnStr FROM $table LIMIT $skip, $take";
            $result = mysqli_query($db, $sql);
            $data = array();
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
            }
            $db->close();
            return $data;
        }  catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}