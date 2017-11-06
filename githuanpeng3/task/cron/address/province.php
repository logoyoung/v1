<?php
require __DIR__.'/../../bootstrap/i.php';
use lib\address\Province as ProvinceDao;

class province
{

    const PROVINCE_CONFIG = __DIR__.'/../../../include/config/address/province.php';

    public function getProvinceData()
    {
        $dao = new ProvinceDao();
        return $dao->getAllProvinceData();
    }

    public function run()
    {
        $provinceData = $this->getProvinceData();
        if(!$provinceData)
        {
            echo 'get province data db error';
            return false;
        }

        $conf = $this->buildProvinceConf($provinceData);
        $r = file_put_contents(self::PROVINCE_CONFIG,$conf);
        if(!$r)
        {
             echo "build province data config error \n";
             die;
        }

        $config = require self::PROVINCE_CONFIG;

        if($config == $provinceData)
        {
            echo "build province data config success\n";
        } else
        {
            echo "build province data config error\n";
        }

    }

    public function buildProvinceConf($conf)
    {
        $conf = var_export($conf, true);
        $conf = <<<EOT
<?php
return {$conf};
EOT;
        return $conf;
    }
}

$obj = new province();
$obj->run();