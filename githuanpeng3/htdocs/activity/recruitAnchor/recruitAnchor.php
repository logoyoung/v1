<?php
include __DIR__."/../../init.php";
$domain = DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain'];
header("Location: $domain/static/activity/recruitAnchor/recruitAnchor.php?".http_build_query($_GET));