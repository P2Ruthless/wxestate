<?php
namespace Admin\Controller;

class HouseRentController extends ArticleController{
	public function _initialize(){
		parent::_initialize();
		$this->cate_id = 10001;
		$_REQUEST['cate_id'] = $this->cate_id;
	}
	public function index($cate_id = 10001){
		//获取左边菜单
        $this->getMenu();

        if($cate_id===null){
            $cate_id = $this->cate_id;
        }

        //获取模型信息
        $model = M('Model')->getByName('houseRent');

        //解析列表规则
        $fields = array();
        $grids  = preg_split('/[;\r\n]+/s', $model['list_grid']);
        foreach ($grids as &$value) {
            // 字段:标题:链接
            $val      = explode(':', $value);
            // 支持多个字段显示
            $field   = explode(',', $val[0]);
            $value    = array('field' => $field, 'title' => $val[1]);
            if(isset($val[2])){
                // 链接信息
                $value['href']  =   $val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
            }
            if(strpos($val[1],'|')){
                // 显示格式定义
                list($value['title'],$value['format'])    =   explode('|',$val[1]);
            }
            foreach($field as $val){
                $array  =   explode('|',$val);
                $fields[] = $array[0];
            }
        }

        // 过滤重复字段信息 TODO: 传入到查询方法
        $fields = array_unique($fields);

        //获取对应分类下的模型
        if(!empty($cate_id)){   //没有权限则不查询数据
            //获取分类绑定的模型
            $models         =   get_category($cate_id, 'model');
            $allow_reply    =   get_category($cate_id, 'reply');//分类文档允许回复
            $pid            =   I('pid');
            if ( $pid==0 ) {
                //开发者可根据分类绑定的模型,按需定制分类文档列表
                $template = $this->indexOfArticle( $cate_id ); //转入默认文档列表方法
                $this->assign('model',  explode(',',$models));
            }else{
                //开发者可根据父文档的模型类型,按需定制子文档列表
                $doc_model = M('Document')->where(array('id'=>$pid))->find();

                switch($doc_model['model_id']){
                    default:
                        if($doc_model['type']==2 && $allow_reply){
                            $this->assign('model',  array(2));
                            $template = $this->indexOfReply( $cate_id ); //转入子文档列表方法
                        }else{
                            $this->assign('model',  explode(',',$models));
                            $template = $this->indexOfArticle( $cate_id ); //转入默认文档列表方法
                        }
                }
            }

            $this->assign('list_grids', $grids);
            $this->assign('model_list', $model);
            // 记录当前列表页的cookie
            Cookie('__forward__',$_SERVER['REQUEST_URI']);
            $this->display($template);
        }else{
            $this->error('非法的文档分类');
        }
	}
}