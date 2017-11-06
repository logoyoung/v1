<?php
require __DIR__.'/../../bootstrap/i.php';
use lib\address\City as CityDao;

class city
{

    const CITY_CONFIG = __DIR__.'/../../../include/config/address/city.php';

    public function getCityData()
    {
        $cityDao = new CityDao();
        return $cityDao->getAllCityData();
    }

    public function run()
    {
        $cityData = $this->getCityData();
        if(!$cityData)
        {
            echo 'get cityData db error';
            return false;
        }

        $conf = $this->buildCityConf($cityData);
        $r = file_put_contents(self::CITY_CONFIG,$conf);
        if(!$r)
        {
             echo "build city data config error \n";
             die;
        }

        $config = require self::CITY_CONFIG;

        if($config == $cityData)
        {
            echo "build city data config success\n";
        } else
        {
            echo "build city data config error\n";
        }

    }

    public function buildCityConf($conf)
    {
        $conf = var_export($conf, true);
        $conf = <<<EOT
<?php
return {$conf};
EOT;
        return $conf;
    }
}

$obj = new city();
$obj->run();