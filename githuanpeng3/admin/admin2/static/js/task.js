/**
 * Created by hantong on 16/4/28.
 */



var taskBox = {};
!function(a){
    $conf = conf.getConf();
    var taskStatus = {
        0:{
            0:'task-undo',
            1:'task-btn-do',
            2:'去完成'
        },
        1:{
            0:'task-receive',
            1:'task-btn-receive',
            2:'可领取'
        },
        2:{
            0:'task-finish',
            1:'task-btn-finish',
            2:'已完成'
        }
    }

    taskBox.modalHtml = {};
    taskBox.modalHtml.head = function(){
        return '<div class="taskModal-head"><span class="taskModal-title">新手任务</span> <a class="task-close" href="javascript:;"></a> </div>';
    }
    taskBox.modalHtml.body = function(list){
        var status = taskStatus;

        var html = '';
        for(var i in list){
            var d = list[i];
            html += '<div data-stat='+ d.status+' data-taskid='+d.id+' class="task-one '+status[d.status][0]+'"> <p class="task-title">'+ d.title+'</p> <p class="task-reword-desc">奖励<i>'+ d.bean+'</i>个欢朋豆</p> <button class="'+status[d.status][1]+'">'+status[d.status][2]+'</button> </div>';
        }
        return '<div class="taskModal-body">' + html + '<div class="clear"></div></div>';
    }
    taskBox.modalHtml.foot = function(){
        return '    <div class="taskModal-foot"> <p>＊所有奖励需要绑定手机后领取</p> <span class="ufo"></span> </div>';
    }

    taskBox.set_pos = function(){
        var f = a('#taskModal');
        f.css('margin-left', -f.width()/2 + 'px');
    }

    taskBox.createModal = function(b){
        this.remove();
        Mask.creates();
        Mask.box.css('background-color','rgba(0,0,0,0)');
        a('<div/>',{
            id:'taskModal',
            'class':'taskModal',
            'style':'position:fixed; left:50%; top:100px; z-index:1000;',
            html:b
        }).appendTo(document.body);
        this.set_pos();
    }
    taskBox.remove = function(){
        if(!a('#taskModal')[0]){
            return;
        }
        Mask.remove();
        a('#taskModal').remove();
    }
    var task_open_loading = 0;
    taskBox.open = function(){
        if(task_open_loading){
            return;
        }
        task_open_loading = 1;
        var self = this;
        a.ajax({
            url:$conf.api + 'getMyTaskList.php',
            type:'post',
            dataType:'json',
            data:{
                uid:getCookie('_uid'),
                encpass:getCookie('_enc')
            },
            success:function(d){
                task_open_loading = 0
                var html = self.modalHtml.head() + self.modalHtml.body(d.list) + self.modalHtml.foot();
                self.createModal(html);
                self.initEventHandle();
            },
            error:function(){
                task_open_loading = 0;
            }
        });
    }

    taskBox.initEventHandle = function(){
        var self = this;
        a('#taskModal .task-close').bind('click', function(){
            self.remove()
        });

        a('#taskModal .task-one button').bind('click', function(){
            var stat = parseInt(a(this).parent().data('stat'));
            var taskid = a(this).parent().data('taskid');
            console.log(taskid);
            console.log(stat);
            if(!taskid) return;
            if(stat == 0){
                if($conf.taskUrl[taskid]) {
                    window.open($conf.taskUrl[taskid]);
                }else{
                }
                self.remove();
            }else if(stat == 1){
                taskBox.receive(taskid);
            }

            return;
        });
    }

    taskBox.receive = function(taskid){
        if(!taskid){
            return false;
        }
        //注意要加验证
        $.ajax({
            url:$conf.api + 'getBeanByTask.php',
            type:'post',
            dataType:'json',
            data:{
                uid:getCookie('_uid'),
                encpass:getCookie('_enc'),
                taskID:taskid
            },
            success:function(d){
                if(d.isSuccess){
                    //更新用户余额
                    set_user_hpbean(d.bean);
                    setProperty(d.coin, d.bean);
                    changeStat(taskid);
                    tips('领取成功');
                }
            }
        });

        function changeStat(taskid){
            $('#taskModal .task-one.task-receive').each(function(index, element){
                if($(element).data('taskid') == taskid){
                    $(element).data('stat',2);
                    $(element).removeClass(taskStatus[1][0]).addClass(taskStatus[2][0]);
                    $(element).find('button').removeClass(taskStatus[1][1]).addClass(taskStatus[2][1]).text(taskStatus[2][2]);
                }
            });
        }
    }

}(jQuery);