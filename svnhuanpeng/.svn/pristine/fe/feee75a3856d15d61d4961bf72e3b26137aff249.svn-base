<?php

namespace Common\Model;

class AclAccessModel extends BaseModel
{
    protected $record_write = true;
    public function getType($index = null)
    {
        $hash = [
            '1' => '顶级菜单',
            '2' => '父菜单',
            '3' => '子菜单',
            '4' => '权限',
        ];
        return is_null($index)?$hash:$hash[$index];
    }

    protected $_validate = array(
        array('name', 'require', '名称必填！'),
        array('type', array(1, 2, 3, 4), 'Type值的范围不正确！', 2, 'in'),
    );

    public function create($data = '')
    {
        $data = parent::create($data);
        switch ($this->type) {
            case '1':
                $this->controller = '';
                $this->action = '';
                $this->parent_id = '';
                break;
            case '2':
                $this->controller = '';
                $this->action = '';
                break;
            case '2':
                break;
            case '4':
                $this->action = '';
                break;
        }
        return $data;
    }

}
