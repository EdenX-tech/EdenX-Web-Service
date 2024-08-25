<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
// use app\common\validate\UserValidate;
use app\common\model\Project as ProjectModel;

class Project extends BaseController
{
    public function ProjectList()
    {
        $data = (new ProjectModel())->ProjectList();
        return self::showResCode('获取成功',['list'=>$data]);
    }

    public function createProject()
    {
        $data = (new ProjectModel())->createProject();
        return self::showResCode('获取成功',['list'=>$data]);
    }

    public function editProject()
    {
        $data = (new ProjectModel())->editProject();
        return self::showResCode('获取成功',['list'=>$data]);
    }

    public function deleteProject()
    {
        $data = (new ProjectModel())->deleteProject();
        return self::showResCode('获取成功',['list'=>$data]);
    }
}