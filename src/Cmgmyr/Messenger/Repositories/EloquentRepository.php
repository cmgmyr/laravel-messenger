<?php namespace Cmgmyr\Messenger\Repositories;

abstract class EloquentRepository
{
    /**
     * Eloquent model
     */
    protected $model;
    /**
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }
    /**
     * Fetch a record by id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }
}
