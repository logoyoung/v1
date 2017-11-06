<?php
require __DIR__.'/../../bootstrap/i.php';
while (true) {
    file_put_contents('/tmp/a_test.data', date('Y-m-d H:i:s')."\n", 8);
    sleep(120);
}