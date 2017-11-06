<?php
/**************************************/
require '../../vendor/autoload.php';
use \Wcs\ImageProcess\ImageInfo;
/**************************************/

function print_help() {
    echo "Usage: php image_exif.php [-h | --help] -b <bucketName> -f <fileName>\n";
}
$opts = "hb:f:";
$longopts = array (
    'h',
    'help'
);

$options = getopt($opts, $longopts);
if (isset($options['h']) || isset($options['help'])) {
    print_help();
    exit(0);
}

if (!isset($options['b']) || !isset($options['f']))  {
    print_help();
    exit(0);
}

$bucketName = $options['b'];
$fileName = $options['f'];

print("bucketName: \t$bucketName\n");
print("fileName: \t$fileName\n");

print("\n");


/**************************************/
$client = new ImageInfo();
print_r($client->imgEXIF($bucketName, $fileName));
/**************************************/
print("\n");