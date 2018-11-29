<?php
/**
 * Created by PhpStorm.
 * User: Yurituski
 * Date: 07.11.2018
 * Time: 12:42
 */

namespace App\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrderScope implements Scope
{
    private $column;

    private $direction;

    public function __construct($column, $direction = 'asc')
    {
        $this->column = $column;
        $this->direction = $direction;
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->orderBy($this->column, $this->direction);
    }

}