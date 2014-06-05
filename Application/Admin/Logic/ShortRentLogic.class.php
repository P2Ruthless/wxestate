<?php
namespace Admin\Logic;

class ShortRentLogic extends BaseLogic{
	
    protected $tableName = 'shortrent'; 
    
	public function update($id = 0){
        //echo 'test'; exit;
        /* 获取文章数据 */
        $data = $this->create();
        if($data === false){
            return false;
        }

        /* 添加或更新数据 */
        if(empty($data['id'])){//新增数据
            $data['id'] = $id;
            $id = $this->add($data);
            if(!$id){
                $this->error = '新增短期租房信息失败！';
                return false;
            }
        } else { //更新数据
            $status = $this->save($data);
            if(false === $status){
                $this->error = '更新短期租房信息失败！';
                return false;
            }
        }

        if($data['id']){
            $this->updatePictures($data['id'], I('house_pic', '[]'));
        }

        return true;
    }

    public function autoSave($id = 0){

    }

	private function updatePictures($houseId, $pics){
        $housePicList = json_decode($pics);
        if(empty($housePicList)){
            return;
        }
        
        $picIds = array();
        foreach($housePicList as $pic){
            $picIds[] = $pic->id;
        }
        $picModel = M('Picture');
        $picModel->where(array(
            'id'=>array('IN', $picIds),
            'uid'=>array('EQ', is_login())
            ))
            ->setField('pid', $houseId);
        $thumbPic = $picModel->find($picIds[0]);
        if(empty($thumbPic)){
            return;
        }
        $thumbInfo = getThumbImage($thumbPic['path'], C('HOUSE_PIC_DIMEN.THUMB_WIDTH'), C('HOUSE_PIC_DIMEN.THUMB_HEIGHT'), true);
        if($thumbInfo){
            D('ShortRent', 'Logic')->where('id=%d', $houseId)->setField('thumbnail', '/'.$thumbInfo['src']);
        }
    }
}