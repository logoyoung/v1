<?php
// +----------------------------------------------------------------------
// 兼容bootcss分页
// +----------------------------------------------------------------------
namespace HP\Util;

class Export{
    /**
     * 将数组动态输出至 csv 文件【服务器端输出到浏览器】
     * @param array $data 二维数组
     * @param string $filename 文件名
     */
    static public function outputCsv($data, $filename = 'file.csv') {
        stripos($filename, '.csv')===false and $filename .= '.csv';
        $ua = $_SERVER["HTTP_USER_AGENT"];
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header("Content-Transfer-Encoding:binary");
        header("Content-type:text/csv;charset=utf-8");
        if(preg_match("/MSIE/",$ua))
        {
            header('Content-Disposition: attachment; filename="' . str_replace("+","%20",urlencode($filename)) . '"');
        }
        else if(preg_match("/Firefox/",$ua))
        {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        }
        else
        {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
    
        echo (chr(0xEF).chr(0xBB).chr(0xBF));//设置utf-8 + bom ，处理汉字显示的乱码 
        foreach ($data as $fields) {
            foreach ($fields as $key => $value) {
                echo $value . ',';
            }
            echo "\r\n";
        }
        exit;
    }
    
    /**
     * 将数组动态输出至 csv 文件【服务器端生成文件】
     * @param array $data 二维数组
     * @param string $filename 文件名
     */
    static public function writeCsv($data, $filename = 'file.csv') {
        $fp = fopen($filename, 'w');
        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }
    
    static public function outputXml($str, $filename = 'file.xls')
    {  
	    stripos($filename, '.xls')===false and $filename .= '.xls';
	    $ua = $_SERVER["HTTP_USER_AGENT"];
	    header("Pragma: public");
	    header("Expires: 0");
	    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
	    header("Content-Type:application/force-download");
	    header("Content-Type:application/octet-stream");
	    header("Content-Type:application/download");
	    header("Content-Transfer-Encoding:binary");
	    header("Content-type:text/xml;charset=utf-8");
	    if(preg_match("/MSIE/",$ua))
	    {
	    	header('Content-Disposition: attachment; filename="' . str_replace("+","%20",urlencode($filename)) . '"');
	    }
	    else if(preg_match("/Firefox/",$ua))
	    {
	    	header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
	    }
	    else
	    {
	    	header('Content-Disposition: attachment; filename="' . $filename . '"');
	    }
	    
	    echo (chr(0xEF).chr(0xBB).chr(0xBF));//设置utf-8 + bom ，处理汉字显示的乱码
	    echo $str;
	    exit;
    }
    
}
