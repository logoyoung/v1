#!/usr/bin/env bash
/usr/bin/rsync -avrn --delete /usr/local/huanpeng-pub/. 172.20.28.118::huanpeng
/usr/bin/rsync -avrn --delete /usr/local/huanpeng-pub/. 172.20.28.119::huanpeng