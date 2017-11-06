<?php
require '../../vendor/autoload.php';
use Wcs\PersistentFops\Fops;

function print_help() {
    echo "Usage: php persistent_status.php [-h | --help] -p <persistentId>\n";
}
$opts = "hp:";
$longopts = array (
    'h',
    'help'
);

$options = getopt($opts, $longopts);
if (isset($options['h']) || isset($options['help'])) {
    print_help();
    exit(0);
}

if (!isset($options['p']))  {
    print_help();
    exit(0);
}

$persisetntId = $options['p'];

print("persistentId: \t$persisetntId\n");

print("\n");



print_r(Fops::status($persisetntId));
print("\n");