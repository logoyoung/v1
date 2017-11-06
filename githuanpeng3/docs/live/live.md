# 欢朋发直播相关模块



* ### [欢朋直播－直播、直播流状态](#huanpengLive-liveStatus)
* ### [欢朋直播－发起直播](#huanpengLive-start)
* ### [欢朋直播－结束直播](#huanpengLive-stop)
* ### [欢朋直播－异常处理](#huanpengLive-exceptional)
* ### [欢朋直播－接口](#huanpengLive-interface)
* ### [欢朋直播－设计图](#huanpengLive-design)




<span id="huanpengLive-liveStatus"></span>
## 欢朋直播－直播、直播流状态
### 直播状态
* ### LIVE_CREATE   直播创建 0         
* ### LIVE_TIMEOUT  直播超时 230 
* ### LIVE          直播中 100
* ### LIVE_STOP     直播结束 110
* ### LIVE_TO_FLV   FLV片段已生成 120
* ### FLV_TO_VIDEO  FLV已生成录像 130
* ### VIDEO_TO_POSTER  截图完成 140
* ### LIVE_COMPLETE   直播活动结束 200
* ### VIDEO_MERGE_FAILED 录像合并出错 210
* ### VIDEO_TO_POSTER_FAILED 录像截图出错 220

### 直播流状态
* ### STREAM_CREATE       直播流创建 0     
* ### STREAM_START        直播流开始 100     
* ### STREAM_DISCONNECT   直播流中断 200     
* ### STREAM_TIMEOUT      直播流超时 210     
* ### STREAM_COVER        直播流被覆盖 220     
* ### LIVE_CREATE   直播创建 0     
* ### LIVE_CREATE   直播创建 0     
* ### LIVE_CREATE   直播创建 0     



## 待定
