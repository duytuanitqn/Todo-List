<?php

namespace Modules\TodoList\Controller;

use Core\Controller\BaseController;
use Modules\TodoList\Models\TodoList;
use Core\Helper\Validate;

class TodoListController extends BaseController
{
    const DEFAULT_CURRENT_PAGE = 1;
    const DEFAULT_LIMIT_PAGE = 10;

    protected $todoList;

    protected $validate;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // start session
        session_start();
        // init todo list model
        $newTodoList = new TodoList();
        $this->todoList = $newTodoList;

        // init validate
        $newValidate = new Validate();
        $this->validate = $newValidate;
    }

    /**
     * Display todo list
     *
     * @return mixed
     */
    public function index()
    {
        try {
            $currentPage = isset(app('request')->query['page']) ? (int)app('request')->query['page'] : $this::DEFAULT_CURRENT_PAGE;
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

    /**
     * Display view create a new todo list.
     *
     * @return void
     */
    public function create()
    {
        try {
            $error = array();
            if(isset($_SESSION["errors"])){
                $error = $_SESSION["errors"];
                unset($_SESSION["errors"]);
            }
            $data = [
                'errors' => $error
            ];
            $this->loadView("TodoList\Views\create.html", $data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * Create a new todo list.
     *
     * @return void
     */
    public function store()
    {
        try {
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                $workName = $this->validate->trimInput(app('request')->body['work_name']);
                $startingDate = $this->validate->trimInput(app('request')->body['starting_date']);
                $endingDate = $this->validate->trimInput(app('request')->body['ending_date']);
                $errorValidate = $this->validateRequestCreateTodoList($workName, $startingDate, $endingDate);
                if(!empty($errorValidate)) {
                    $_SESSION["errors"] = $errorValidate;
                    $this->redirect('/todo-list/create');
                } else {
                    $args = [
                        "work_name" => $workName,
                        "starting_date" => $startingDate,
                        "ending_date" => $endingDate
                    ];
                    $this->todoList->createTodoList($args);
                    $this->redirect('/todo-list/index');
                }
            } else {
                echo "Method not allow";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * Validate request create new todo list.
     *
     * @param $workName     string [work name]
     * @param $startingDate date   [starting date]
     * @param $endingDate   date   [ending date]
     *
     * @return array
     */
    private function validateRequestCreateTodoList($workName, $startingDate, $endingDate)
    {
        $error = array();
        if (!$this->validate->required($workName)) {
            $error['work_name'] = 'The work name field is required.';
        }
        if (!$this->validate->required($startingDate)) {
            $error['starting_date'] = 'The starting date field is required.';
        } else if (!$this->validate->date($startingDate)) {
            $error['starting_date'] = 'The starting date is invalid format.';
        }
        if (!$this->validate->required($endingDate)) {
            $error['ending_date'] = 'The ending date field is required.';
        } else if (!$this->validate->date($endingDate)) {
            $error['ending_date'] = 'The ending date is invalid format.';
        }
        if (strtotime($startingDate) > strtotime($endingDate)) {
            $error['starting_date'] = 'The start date must be less than the end date.';
        }
        return $error;
    }

    /**
     * Display view edit todo list
     *
     * @param $id integer [id todo list]
     *
     * @return void
     */
    public function edit($id)
    {
        try {
            $result = $this->todoList->findTodoList($id);
            if(empty($result)) {
                echo "PAGE NOT FOUND";
                die();
            }
            $error = array();
            if(isset($_SESSION["errors"])){
                $error = $_SESSION["errors"];
                unset($_SESSION["errors"]);
            }
            $data = [
                'errors' => $error,
                'result' => $result
            ];
            $this->loadView("TodoList\Views\update.html", $data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * Update todo list
     *
     * @param $id integer [id todo list]
     *
     * @return void
     */
    public function update($id)
    {
        try {
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                $workName = $this->validate->trimInput(app('request')->body['work_name']);
                $startingDate = $this->validate->trimInput(app('request')->body['starting_date']);
                $endingDate = $this->validate->trimInput(app('request')->body['ending_date']);
                $errorValidate = $this->validateRequestCreateTodoList($workName, $startingDate, $endingDate);
                if(!empty($errorValidate)) {
                    $_SESSION["errors"] = $errorValidate;
                    $this->redirect("/todo-list/$id/edit");
                } else {
                    $args = [
                        "work_name" => $workName,
                        "starting_date" => $startingDate,
                        "ending_date" => $endingDate
                    ];
                    if($this->todoList->updateTodoList($id, $args)) {
                        $this->redirect('/todo-list/index');
                    } else {
                        echo "The record update is error";
                    }
                }
            } else {
                echo "Method not allow";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * Delete todo list
     *
     * @param $id integer [id todo list]
     *
     * @return void
     */
    public function delete($id)
    {
        try {
            if($this->todoList->deleteTodoList($id)) {
                $this->redirect('/todo-list/index');
            } else {
                echo "can`t delete record";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }
     /**
     * Get calendar todo list
     *
     * @return mixed
     */
    public function getCalendar()
    {
        $this->loadView("TodoList\Views\calendar.html");
    }

    /**
     * Get events todo list using ajax
     *
     * @return object
     */
    public function getEventCalendar()
    {
        $results = $this->todoList->getTodoList();
        $events = array(
            'date_now' => date('Y-m-d'),
        );
        foreach($results as $item) {
            $constraint = 'availableForEvent'.$item['id'];
            if(strtotime(date("Y-m-d", strtotime($item['created_at']))) < strtotime($item['starting_date'])) {
                $events['events'][] = [
                    'title' => 'Task Name:'.$item['work_name'].', Status: (Planning)',
                    'start' => date("Y-m-d", strtotime($item['created_at'])),
                    'end' => $item['starting_date'],
                    "constraint" => $constraint, // defined below
                    "color" => '#006666'
                ];
            }
            $events['events'][] = [
                'title' => 'Task Name:'.$item['work_name'].', Status: (Doing)',
                'start' => $item['starting_date'],
                'end' => $item['ending_date'],
                "constraint" => $constraint, // defined below
                "color" => '#FF3300'
            ];
            $events['events'][] = [
                'title' => 'Task Name:'.$item['work_name'].', Status: (Complete)',
                'start' => $item['ending_date'],
                "constraint" => $constraint, // defined below
                "color" => '#00AA00'
            ];
            $events['events'][] = [
                'id' => $constraint,
                'start' => $item['starting_date'],
                'end' => $item['ending_date'],
                'rendering' => 'background'
            ];
        }
        print(json_encode($events));
    }
}