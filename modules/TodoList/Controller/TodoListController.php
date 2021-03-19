<?php

namespace Modules\TodoList\Controller;

use Core\Controller\BaseController;
use Modules\TodoList\Models\TodoList;

class TodoListController extends BaseController
{
    const DEFAULT_CURRENT_PAGE = 1;
    const DEFAULT_LIMIT_PAGE = 10;

    protected $todoList;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // init todo list model
        $newTodoList = new TodoList();
        $this->todoList = $newTodoList;
    }

    /**
     * Display todo list
     *
     * @return mixed
     */
    public function index()
    {
        try {
            $currentPage = isset(app('request')->query['page']) ? app('request')->query['page'] : $this::DEFAULT_CURRENT_PAGE;
            $totalRecords = $this->todoList->countTotalRecord();
            $data = [];
            if ($totalRecords > 0) {
                $totalPage = ceil($totalRecords / $this::DEFAULT_LIMIT_PAGE);
                if ($currentPage > $totalPage){
                    $currentPage = $totalPage;
                }else if ($currentPage < $this::DEFAULT_CURRENT_PAGE){
                    $currentPage = $this::DEFAULT_CURRENT_PAGE;
                }
                $start = ($currentPage - 1) * $this::DEFAULT_LIMIT_PAGE;
                $data = [
                    'results' => $this->todoList->paginate($start, $this::DEFAULT_LIMIT_PAGE),
                    'total_record' => $totalRecords,
                    'total_page' => $totalPage,
                    'current_page' => $currentPage,
                    'previous' => $currentPage - 1,
                    'next' => $currentPage + 1
                ];
            }
            $this->loadView("TodoList\Views\index.html", $data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }
}