<?php
require '../../../include/init.php';

class zhimaCrtQuery
{

    public function run()
    {
        render_json('{
  "bizNo": "ZM201703313000000191900768449771",
  "body": "{\"success\":true,\"biz_no\":\"ZM201703313000000191900768449771\"}",
  "params": {
    "params": "Gejqzwt7S+sxsa12F73kGtLasGl/D7Inu4QB842ujACpV4POjxEzgFC9zof+3dbngO8GZJqUrNcUX9Y/yAxo9fDBtJk//uR9rhvnJ2PXOZhJM8qfJqNbzFn3mPyVCHQORdKOtdmkMjDuRIu5sda0qpLMRW8ANII2r6xzAbmX1clpBuQ0+KCw6+dmRAmF2jaFFJzRnkwD6uj8WoI2YZB2OR/lBcCVcAbuoUVNkr45mP9Q7wW/68/0r5fOPIZdKzBy61oJsRfMXboro3BFKUyhgC0wS2he3Hkz7aNjP0baWCVD5TIOvThLiTphHV7/Sx9NddmTBKMuwHy0g7JOjPvpbnLmi6w/Ol1SNBHZJww+m1+YjePxSAnU5KH5GENZwF+nPoLRXUkA/zQK8/3DEwg4zNJKfmx2huKFInzhFFSwXcnn1KmpcxxJlvUOF20K9iun5eE1rX6ZBrR1lbotSacGLcBNPxiLWGO/Pd1qWFoCOmT/JX3LxLf9k/R876xtFzWL"
  },
  "success": true
}');
    }
}

$obj = new zhimaCrtQuery();
$obj->run();