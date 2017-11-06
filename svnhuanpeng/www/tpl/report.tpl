<textarea id="jsTemplate-report" style="display:none;">
  <div id="report-modal">
    <div class="report-screen">
        <div class="report-contain">
                <div class="report-body">
                  <div style="width: 100%;float: left;padding:10px 30px 0 30px;">
                    <div style="float: left;font-size: 12px;width: 330px;">您可以向欢朋直播官方举报，或者是向全国文化市场举报平台进行举报。</div>
                    <div style="float: left;width: 180px;height: 50px;padding: 0px 25px;">
                        <a href="http://jb.ccm.gov.cn" target="_blank"><img src="../static/img/src/xkz/jubao.png"></a>
                    </div>
                  </div>
                    <div class="report_box">
                      <table>
                        <tr>
                          <td><p class="report-p">主播昵称</p></td>
                          <td><p id="report_name" style="text-align:left;">**</p></td>
                        </tr>
                        <tr>
                          <td><p class="report-p">房间号</p></td>
                          <td><p id="report_room" style="text-align:left;">****</p></td>
                        </tr>
                        <tr style="height:35px;">
                          <td><p class="report-p" style="margin-top:4px;line-height:43px;">举报原因</p></td>
                          <td><select id="report-select">
                              <option>政治敏感</option>
                              <option>色情低俗</option>
                              <option>内容侵权</option>
                              <option>广告骚扰</option>
                              <option>其它</option>
                          </select>
                        </td>
                        </tr>
                        <tr>
                          <td style="font-size:12px;text-align:right;vertical-align:top;">详细说明</td>
                          <td><textarea class="report-text" placeholder="请输入举报详述" onfocus="this.placeholder=''" onblur="this.placeholder='请输入举报详述'"></textarea></td>
                        </tr>
                        {* <tr>
                          <td><p>上传截图</p></td>
                          <td><input type="file" accept="image/*"></td>
                        </tr> *}
                      </table>
                    </div>

                </div>
        </div>

    </div>
</div>
</textarea>
