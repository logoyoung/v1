<?php
/**
 * PHP通用分页类
 * $page_size:每页显示记录数
 * $total :总记录数
 * 语法:
 * new pages($page_size,$total)
 * 可用返回值:
 * $page_html	(分页连接: 首页 上一页 下一页 末页)
 */
class Pagination{
	
	private $page_size;  //每页显示数量
	private $total;     //总数量
	private $page_total;  //总页数
	private $page;  //
	private $url;  //当前URL
	private $pre_page = 'page';  //传递页码的参数
	private $page_info;   //
	private $page_html;

	function __construct($total, $page_size = 10){
		$this->page_size	=	$page_size;
		$this->total		=	$total;
		$this->page_total	=	ceil($total/$page_size);
		$this->page			=	$this->get_page();
		$this->url			=	$this->get_url();
		$this->page_info	=	$this->page_info();
	}
	
	//获取当前页码
	private function get_page(){
		$page = isset($_GET[$this->pre_page]) ? $this->f_GET($this->pre_page) : 1;
		$page = $page < 1 ? 1 : $page;
		$page = $page>$this->page_total ? $this->page_total : $page;
		return $page;
	}
	
	//生成翻页html代码
	public function page_html(){
		$page_html = '<ul class="pagination pull-right">';
		
		$page_info = $this->page_info; 
		if (!empty($page_info['first'])){
			if ($this->page=="" || $this->page==1){
				$page_html	.=	"<li>".$page_info['first']."</li>";
			}
			else {
				$page_html	.=	"<li class='page_first'><a href='?".$this->url['first']."'>".$page_info['first']."</a></li>";
			}
		}//首页
		
		if (!empty($page_info['pro'])){
			if ($this->page=="" || $this->page==1){
				$page_html	.=	"<li class='page_pro'>".$page_info['pro']."</li>";
			}
			else{
				$page_html	.=	"<li class='page_pro'><a href='?".$this->url['pro']."'>".$page_info['pro']."</a></li>";
			}
		}//上一页
		
		if (!empty($page_info['next'])){
			if ($this->page >= $this->page_total){
				$page_html	.=	"<li class='page_next'>".$page_info['next']."</li>";
			}
			else{
				$page_html	.=	"<li class='page_next'><a href='?".$this->url['next']."'>".$page_info['next']."</a></li>";
			}
		}//下一页
		
		if (!empty($page_info['last'])){
			if ($this->page >= $this->page_total){
				$page_html	.=	"<li class='page_last'>".$page_info['last']."</li>";
			}
			else{
				$page_html	.=	"<li class='page_last'><a href='?".$this->url['last']."'>".$page_info['last']."</a></li>";
			}
		}//最后一页
		
		if (!empty($page_info['t_page'])){
			$page_html	.=	"<li class='t_page'>".str_replace("%i%",$this->page_total,$page_info['t_page'])."</li>";
		}
		if (!empty($page_info['page'])){
			$page_html	.=	"<li class='now_page'>".str_replace("%i%",$this->page,$page_info['page'])."</li>";
		}
		if (!empty($page_info['r_total'])){
			$page_html	.=	"<li class='r_page'>".str_replace("%i%",$this->total,$page_info['r_total'])."</li>";
		}
		$page_html .= '</ul>';
		return $page_html;
		
	}
	
	//获取当前 URL
	private function get_url()
	{
		$str = '';
		$url_str = $_GET;
		$query_string =	array();
		foreach($url_str as $key=>$value){
			if ($key == $this->pre_page){
				continue;
			}
			$str .=	$key."=".$value."&";
		}
		$next_tmp =	$this->page + 1;
		$pro_tmp =	$this->page - 1;
		$last =	$str.$this->pre_page."=".$this->page_total;
		$next =	$str.$this->pre_page."=".$next_tmp;
		$pro = $str.$this->pre_page."=".$pro_tmp;
		$first = $str.$this->pre_page."=1";
		
		$query_string =	array("pro"=>$pro,"next"=>$next,"last"=>$last,"first"=>$first);
		return $query_string;
	}
	
	private function page_info(){
		$arr	=	array(
			"first" => "首页",
			"next" => "下一页",
			"pro" => "上一页",
			"last" => "末页",
			"t_page" => "共 %i% 页",
			"page" => "当前第 %i% 页",
			"r_total"=>	"共 %i% 条"
		);
		return $arr;
	}
	
	//过滤函数
	private function f_Get($GET){
		$GET = $_GET[$GET];
		return preg_replace("/[^0-9]+/i", "", $GET);
	}
}