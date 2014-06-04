<?php
namespace Admin\Controller;

class HouseRentController extends ArticleController{
    public function _initialize(){
        parent::_initialize();
        $this->cate_id = 10001;
        $_REQUEST['cate_id'] = $this->cate_id;
    }
    public function index($city = 517, $area = '0', $price = '0', $rentType = '0', $room = '0', $pn = 1, $sort = ''){
        $this->getMenu();

        $map = array();

        $map['hr.city'] = (int)$city;
        $map['doc.category_id'] = (int)$this->cate_id;
        $map['doc.status'] = array('NEQ', -1);

        if($area != '0'){
            $map['_string'] = "hr.area=$area OR hr.busi_area=$area";
        }
        if($price != '0'){
            $priceRange = explode('-', $price);
            if(is_numeric($priceRange[0]) && is_numeric($priceRange[1])){
                $map['hr.price'] = array('BETWEEN', array_map(function($v){return (int)$v;}, $priceRange));
            }elseif(is_numeric($priceRange[0])){
                $map['hr.price'] = array('GT', (int)$priceRange[0]);
            }elseif(is_numeric($priceRange[1])){
                $map['hr.price'] = array('LT', (int)$priceRange[1]);
            }
        }
        if($rentType != '0'){
            $map['hr.rent_type'] = (int)$rentType;
        }
        if($room != '0'){
            $map['hr.bed_room'] = (int)$room;
        }

        $sortField = 'hr.id';
        $sortDir = 'desc';
        if(!empty($sort)){
            $sortInfo = explode('-', $sort);
            if(isset($sortInfo[0])){
                $sortField = $sortInfo[0];
            }
            if(isset($sortInfo[1])){
                $sortDir = $sortInfo[1];
            }
        }

        $scope = get_category($this->cate_id, 'list_row');

        $model = D('HouseRent', 'Logic');

        $totalCount = $model->alias('hr')
            ->join('to_document doc on hr.id=doc.id')
            ->where($map)
            ->count(1);
        $dataList = $model->field('doc.id,doc.title,doc.status,hr.price,hr.bed_room,hr.live_room,hr.rent_type,area.name area_name,busi_area.name busi_area_name')
            ->alias('hr')
            ->join('to_document doc on doc.id=hr.id')
            ->join('to_district busi_area on hr.busi_area=busi_area.id', 'LEFT')
            ->join('to_district area on hr.area=area.id', 'LEFT')
            ->where($map)
            ->order("$sortField $sortDir")
            ->page($pn, $scope)
            ->select();

        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->meta_title = '房屋租赁列表';
        $this->assign('dataList', $dataList);
        $this->assign('city', $city);
        $this->assign('area', $area);
        $this->assign('price', $price);
        $this->assign('rentType', $rentType);
        $this->assign('room', $room);
        $this->assign('totalCount', $totalCount);
        $this->assign('pn', $pn);
        $this->assign('scope', $scope);
        $this->assign('allow', get_category($this->cate_id, 'allow_publish'));

        $this->display();
    }

    public function add(){
        $this->getMenu();

        $info = array();
        $info['pid'] = 0;
        $info['category_id'] = 10001;
        $info['model_id'] = 5;
        $info['rent_type'] = 1;

        $this->assign('info', $info);
        $this->assign('city', 517);

        $this->display('edit');
    }

    public function edit(){
        $id = I('id', '0');
        if(empty($id)){
            $this->error('参数不正确');
        }

        $this->getMenu();

        $houseRent = M('DocumentHouserent')->field('*')->alias('hr')
            ->join('to_document doc on doc.id=hr.id')
            ->where(array('hr.id'=>(int)$id))
            ->find();

        $housePics = M('Picture')->field('id,path')
            ->where(array('pid'=>$houseRent['id']))
            ->select();

        if(!empty($housePics)){
            $houseRent['house_pic'] = json_encode($housePics);
        }else{
            $houseRent['house_pic'] = '[]';
        }


        if(empty($houseRent)){
            $this->error('数据不存在');
        }

        $this->meta_title = '房屋租赁编辑';

        $this->assign('city', 517);

        $this->assign('info', $houseRent);

        $this->display();
    }

    public function update(){
        if(!IS_POST){
            $this->error('错误的请求类型');
        }

        if(isset($_POST['feature']) && is_array($_POST['feature'])){
            $_POST['feature'] = array2string($_POST['feature']);
        }
        if(isset($_POST['add_on']) && is_array($_POST['add_on'])){
            $_POST['add_on'] = array2string($_POST['add_on']);
        }

        $res = D('Document')->update();
        if(!$res){
            $this->error(D('Document')->getError());
        }else{
            $this->success($res['id']?'更新成功':'新增成功', Cookie('__forward__'));
        }
    }
}