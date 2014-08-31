<?php

namespace pjdietz\RestCms\Test\Unit;

use pjdietz\RestCms\Model;
use pjdietz\RestCms\Test\TestCases\TestCase;

class ModelTest extends TestCase
{
    public function testConstructWithNull()
    {
        $model = new ModelTestModel();
        $this->assertNotNull($model);
    }

    public function testConstructWithObject()
    {
        $rep = new \stdClass();
        $rep->id = 7;
        $model = new ModelTestModel($rep);
        $this->assertSame($model->id, $rep->id);
    }

    public function testConstructWithArray()
    {
        $rep = ["id" => 7];
        $model = new ModelTestModel($rep);
        $this->assertSame($model->id, $rep["id"]);
    }
}

class ModelTestModel extends Model
{
    public function __construct($representation = null)
    {
        parent::__construct($representation);
    }

    protected function prepareInstance()
    {
    }
}
