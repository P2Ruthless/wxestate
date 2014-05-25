<?php
namespace Admin\Controller;

use Admin\Model\DistrictModel;

class DistrictController extends AdminController{
    public function lists($pid = 1){
        $District = D('District');
        $parentDistrict = $District->field(true)->find($pid);

        if(empty($parentDistrict)){
            $this->error('区域数据不存在');
        }
        //get path from root
        $rootPath = array($parentDistrict);
        $curParent = $parentDistrict;
        while($curParent['id'] != $curParent['pid']){
            $curParent = $District->field(true)->where(array('id'=>$curParent['pid']))->find();
            if(!empty($curParent)){
                array_unshift($rootPath, $curParent);
            }else{
                break;
            }
        }

        $parentDistrict['rootPath'] = $rootPath;

        // fetch children
        $districtList = $District->field(true)
            ->where("pid=%d AND pid<>id", $pid)
            ->select();

        $parentDistrict['children'] = $districtList;

        $this->meta_title = '区域列表';
        $this->assign('parentDistrict', $parentDistrict);

        $this->display();
    }

    public function changeState($id, $state){
        $District = D('District');
        $district = $District->field(true)->find($id);
        if(empty($district)){
            $this->error('数据不存在');
        }
        if($state != $district['inactive'] && ($state == 'Y' || $state == 'N')){
            $District->where(array('id'=>(int)$id))->setField('inactive', $state);
        }

        $this->success('操作成功', U('lists', array('pid'=>$district['pid'])));
    }

    public function add($pid){
        $District = D('District');
        $district = $District->field(true)->find($pid);

        if(empty($district)){
            $this->error('参数不正确，父区域不存在');
        }

        $this->meta_title = '新增区域';

        $this->assign('parentDistrict', $district);

        $this->display('edit');
    }

    public function edit($id){
        $District = D('District');
        $district = $District->field(true)->find($id);

        if(empty($district)){
            $this->error('数据不存在');
        }

        $this->meta_title = '编辑区域';

        $this->assign('district', $district);

        $this->display();
    }

    public function update(){
        if(!IS_POST){
            $this->error('错误的请求类型');
        }

        $District = D('District');
        $data = $District->create();

        if($data){
            if(isset($data['id']) && !empty($data['id'])){
                $District->save();
            }else{
                $District->add();
            }
            $this->success('操作成功', U('District/lists', array('pid'=>$data['pid'])));
        }else{
            $this->error($District->getError());
        }
    }

}