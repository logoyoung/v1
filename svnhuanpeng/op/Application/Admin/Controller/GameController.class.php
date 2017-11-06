<?php

namespace Admin\Controller;
use \HP\Op\Game;


class GameController extends BaseController{

    protected $pageSize = 10;
    public function _access()
    {
        return [
           'typelist' => ['typelist'],
           'typesave' => ['typelist'],
           'gamelist' => ['gamelist'],
           'gamesave' => ['gamelist'],
			'publish'=>['gamelist'],
			'unpublish'=>['gamelist'],
        ];
    }
    
    
    public function typelist(){
        $dao = D('Gametype');
        if($gametid = I('get.gametid')){
            $where['gametid'] = $gametid;
        }
        if($name = I('get.name')){
            $where['name'] = ['like',"%$name%"];
        }
        if($export = I('get.export')){//导出数据
            $datas = $dao
            ->where($where)
            ->order('gametid desc ')
            ->select();
        }else{
            $count = $dao
            ->where($where)
            ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $datas = $dao
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('gametid desc  ')
            ->select();
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('游戏类型ID','名称','icon');
            foreach ($datas as $data) {
                $excel[] = array($data['gametid'],$data['name'],$data['icon']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'游戏类型列表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->display();
    }
    public function gamelist(){
        $dao = D('Game');
        $getTypeinfo = \HP\Op\Game::getTypeinfo();
        if($gaimeid = I('get.gameid')){
            $where['gameid'] = $gameid;
        }
        if($gametid = I('get.gametid')){
            $where['gametid'] = $gametid;
        }
        if($name = I('get.name')){
            $where['name'] = ['like',"%$name%"];
        }
		$gstatus=I('get.gstatus')?I('get.gstatus'):'';
        if(in_array($gstatus,array(1,2))){
			if($gstatus==1){ //下架
				$where['status'] = 1;
			}else{
				$where['status'] = 0;
			}
		}
        if($description = I('get.description')){
            $where['description'] = ['like',"%$description%"];
        }
        if($export = I('get.export')){//导出数据
            $results = $dao
            ->where($where)
            ->order('gameid desc ')
            ->select();
        }else{
            $count = $dao
            ->where($where)
            ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $results = $dao
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('gameid  desc ')
            ->select();
        }
        
        foreach ($results as $result){
            $data = $result;
            $data['type'] = $getTypeinfo[$data['gametid']]['name'];
            $data['gamepics'] = explode(',', $data['gamepic']);
            $datas[] = $data;
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('游戏ID','名称','类型','描述');
            foreach ($datas as $data) {
                $excel[] = array($data['gameid'],$data['name'],$data['type'],$data['description']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'游戏列表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
		$this->gstatus = array('1'=>'已下架','2'=>'已上架');
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->display();
    }
    
    
    public function typesave(){
        $dao = D('Gametype');
        $id = is_numeric(I('get.id'))?I('get.id'):null;
        if(IS_POST){
            $message = ['status'=>0,'info'=>'操作失败'];
            if($dao->create()){
                if(!I('post.name')){
                    $message['info'] ='名称不能为空！';
                    return $this->ajaxReturn($message);
                }
                if(!I('post.icon')){
                    $message['info'] ='图片不能为空！';
                    return $this->ajaxReturn($message);
                }
                if($id){
                    $res = $dao->where('gametid='.$id)->save();
                }else{
                    $res = $dao->add();
                }
                if($res){
                    $message['status'] = 1;
                    $message['info'] = '操作成功';
                }
            }
            return $this->ajaxReturn($message);
        }
        $assign = $id?$dao->find($id):[];
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->assign($assign);
        $this->display();
        
    }
    
    public function gamesave(){
        $dao = D('Game');
        $typeinfo = Game::getTypeinfo();
        $id = is_numeric(I('get.id'))?I('get.id'):null;
        if(IS_POST){
            $message = ['status'=>0,'info'=>'操作失败'];
            $gameid = I("post.gameid");
            if($dao->create()){
                $dao->ord = 1;//默认ord
                if($gameid){
                    $res = $dao->where('gameid='.$gameid)->save();
                }else{
                    $res = $dao->add();
                }
                if(false !==$res){
					$this->update_due($gameid,I("post.icon"),I("post.iconx"));
                    $message['status'] = 1;
                    $message['info'] = '操作成功';
                }
            }
            return $this->ajaxReturn($message);
        }
        $assign = $id?$dao->find($id):[];
        if($assign['gamepic'])$assign['gamepics'] = explode(',', $assign['gamepic']);
        $this->assign($assign);
        $this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $this->typeinfo = $typeinfo;
        $this->display();
    
    }


    public function update_due($gameid,$icon,$icon2x){
		$dao=D('due_game');
		$dao->where("gameid=$gameid")->save(array('icon'=>$icon,'iconx'=>$icon2x));
	}

	/**
	 * 发布
	 */
	public function publish(){
		$id = I( 'post.id' ) ? I( 'post.id' ) : '0'; // 资讯id
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$dao=D('game');
		$res=$dao->where("gameid=$id")->save(array('status'=>0));
		if($res){
			$message = [ 'status' => 1, 'info' => '操作成功' ];
		}else{
			$message = [ 'status' => 0, 'info' => '操作失败' ];
		}
		return $this->ajaxReturn( $message );
	}

	/**
	 * 取消发布
	 */
	public function unpublish(){
		$id = I( 'post.id' ) ? I( 'post.id' ) : '0'; // 资讯id
		if( !$id )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		$dao=D('game');
		$res=$dao->where("gameid=$id")->save(array('status'=>1));
		if($res){
			$message = [ 'status' => 1, 'info' => '操作成功' ];
		}else{
			$message = [ 'status' => 0, 'info' => '操作失败' ];
		}
		return $this->ajaxReturn( $message );
	}
    

}
