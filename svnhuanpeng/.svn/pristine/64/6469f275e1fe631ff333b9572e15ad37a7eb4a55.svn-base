<?php
require __DIR__.'/../../bootstrap/i.php';
use lib\anchor\AnchorLevel;
class level
{

    const USER_LEVEL_CONFIG = __DIR__.'/../../../include/config/level/anchor.php';

    public function getLevelData()
    {
        $levelDao = new AnchorLevel();
        return $levelDao->getLevelData();
    }

    public function run()
    {
        $levelData = $this->getLevelData();
        if(!$levelData)
        {
            echo 'get levelData db error';
            return false;
        }

        $levelConf = $this->buildLevelConf($levelData);
        $r = file_put_contents(self::USER_LEVEL_CONFIG,$levelConf);
        if(!$r)
        {
             echo "build anchor level config error \n";
             die;
        }

        $config = require self::USER_LEVEL_CONFIG;
        if($config == $levelData)
        {
            echo "build anchor level success\n";
        } else
        {
            echo "build anchor level config error\n";
        }

    }

    public function buildLevelConf($levelData)
    {
        $levelData = var_export($levelData, true);
        $conf = <<<EOT
<?php
    return {$levelData};
EOT;
        return $conf;
    }
}

$obj = new level();
$obj->run();