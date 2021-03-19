<?php

namespace Modules\TodoList\Models;

use Core\Model\BaseModel;

class TodoList extends BaseModel
{
    const TABLE = 'todo_list';
    
    protected $db;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->db = $this->connectDB();
    }
    
    /**
     * Create a new todo list.
     *
     * @return boolean
     */
    public function createTodoList($args)
    {
        $this->insert($this::TABLE, $args);
    }

    /**
     * Get todo list paginate.
     * 
     * @param $skip integer [skip record]
     * @param $take integer [take record]
     *
     * @return array
     */
    public function paginate($skip, $take)
    {
        return $this->limit($this::TABLE, ['id', 'work_name', 'starting_date', 'ending_date', 'created_at'], $skip, $take);
    }

    /**
     * Count total record todo list.
     *
     * @return integer
     */
    public function countTotalRecord()
    {
        return $this->count($this::TABLE);   
    }
    
    /**
     * Delete todo list record.
     * 
     * @param $id integer [id todo list]
     *
     * @return boolean
     */
    public function deleteTodoList($id)
    {
        return $this->delete($this::TABLE, $id);
    }

    /**
     * Gell all todo list record.
     *
     * @return array
     */
    public function getTodoList()
    {
        return $this->getAll($this::TABLE, ['id', 'work_name', 'starting_date', 'ending_date', 'created_at']);
    }

    /**
     * Find todo list record.
     * 
     * @param $id integer [id todo list]
     *
     * @return array
     */
    public function findTodoList($id)
    {
        return $this->find($this::TABLE, $id, ['id', 'work_name', 'starting_date', 'ending_date']);
    }

    /**
     * Update todo list record.
     * 
     * @param $id   integer [id todo list]
     * @param $attr array   [array column update]
     *
     * @return boolean
     */
    public function updateTodoList($id, $attr)
    {
        return $this->update($this::TABLE, $attr, $id);
    }

    /**
     * Close connect DB.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->db->close();
    }
    
}