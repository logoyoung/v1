local io = io
local os = os
local ngx = ngx
local mfloor = math.floor
local tonumber = tonumber

local s = ngx.var.arg_s
if not s then ngx.print(-101); ngx.exit(200) end

local save = ngx.var.arg_save
if not save then ngx.print(-102); ngx.exit(200) end
save = tonumber(save)

local flvdir = '/data/content-live/'
local outdir = '/data/r/'
local c = { 
    pro = {
        url = "http://www.huanpeng.com/a/record_notify.php"
    },
    dev = {
        url = "http://dev.huanpeng.com/a/record_notify.php"
    }
}
local ffmpeg = "/usr/local/bin/ffmpeg"
local ffprobe = "/usr/local/bin/ffprobe"
local curl = "/usr/bin/curl"
local rm = "/bin/rm"

local env = ngx.var.env or 'pro'

local fo = flvdir..s..'.flv'
local fomp4 = outdir..s..'.mp4'
local fposter = outdir..s..".jpg"

ngx.print(1)
ngx.eof()

local r1
local duration=0

if save==2 then     -- downloaded mp4 & jpg, delete
    cmd = rm.." -f "..fomp4.." "..fposter
    ngx.log(ngx.NOTICE, cmd)
    r1 = os.execute(cmd)
    ngx.log(ngx.NOTICE, r1)

    ngx.exit(200)
end

if save==0 then     -- given up save video

    cmd = rm.." -f "..fo
    ngx.log(ngx.NOTICE, cmd)
    r1 = os.execute(cmd)
    ngx.log(ngx.NOTICE, r1)

else                -- save video

    -- generate mp4
    cmd = ffmpeg.." -i "..fo.." -c copy -movflags faststart "..fomp4
    ngx.log(ngx.NOTICE, cmd)
    r1 = os.execute(cmd)
    ngx.log(ngx.NOTICE, r1)

    -- get info
    cmd = ffprobe.." -i "..fo.." 2>&1"
    ngx.log(ngx.NOTICE, cmd)
    local fh = io.popen(cmd)
    local info = fh:read("*a")
    fh:close()

    -- take the poster
    local pat = [[Duration: (\d+):(\d+):(\d+)\.\d+,]]
    local m = ngx.re.match(info, pat, "ojs")
    if not m then 
        ngx.log(ngx.WARN, "PROBE failed with info"..info)
        ngx.exit(200)
    end
    duration = m[1]*3600+m[2]*60+m[3]
    local pos = mfloor(duration/2)     -- caculate the image position
    local pos_s = pos - 50
    if pos_s < 0 then pos_s = 0 end
    local pos_i = pos - pos_s
    cmd = ffmpeg.." -ss "..pos_s.." -i "..fo.." -f mjpeg -vframes 1 -ss "..pos_i.." "..fposter
    ngx.log(ngx.NOTICE, cmd)
    r1 = os.execute(cmd)
    ngx.log(ngx.NOTICE, r1)

    -- delete original flv
    cmd = rm.." -f "..fo
    ngx.log(ngx.NOTICE, cmd)
    r1 = os.execute(cmd)
    ngx.log(ngx.NOTICE, r1)

end

-- notify www server
local notify_url = c[env].url.."?save="..save.."&s="..s.."&d="..duration
cmd = curl.." -Ss \""..notify_url.."\" &"
ngx.log(ngx.NOTICE, cmd)
local fh = io.popen(cmd)
fh:close()

