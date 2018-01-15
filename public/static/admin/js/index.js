$(function() {
    //页面基础效果
    var page = {
            run: function() {
                page.slide();
                page.color();
                page.font_color();
                page.jianli_switch();
                if ($("#modify_password").length > 0) {
                    $("#modify_password").validate();
                }
                page.Expiredreminder();
            },
            //子分类显示滑动效果
            slide: function() {
                $('.tree-view').click(function() {
                    $that = $(this);
                    if ($that.children('ul').css('display') == 'block') {
                        $that.children('a').attr('class', 'inactive');
                    } else if ($that.children('ul').css('display') == 'none') {
                        $that.children('a').attr('class', 'active');
                    }
                    $that.children('ul').slideToggle(400);
                    $that.siblings().children('.active').attr('class', 'inactive').siblings().slideToggle(400);
                })
            },
            //大分类点击之后背景颜色变更
            color: function() {
                $('.list .tree-view').click(function() {
                    $that = $(this);
                    $that.addClass('active');
                    $that.siblings('li').removeClass('active').children('.active').attr('class', 'inactive').siblings().slideToggle(400).find('.active').removeClass('active');
                })
            },
            // //子分类点击之后字体颜色变更
            font_color: function() {
                //如果后台采用iframe框架可以开启此效果
                // $('.tree-menu a').click(function(){
                // 	$that =$(this);
                // 	$that.addClass('active');
                // 	$that.parent().siblings().children().removeClass('active');
                // })
                for (i = 0; i < $('.list ul a').length; i++) {
                    if ($('.list ul a').eq(i).attr('href') == window.location.pathname) {
                        var a = $('.list ul a').eq(i);
                        var b = a.parent('.tree-menu')
                        if (b.length > 0) {
                            a.addClass('active');
                            b.show();
                            b.siblings('a').addClass('active');
                            b.parent('.tree-view').addClass('active');
                        } else {
                            a.parent().addClass('active');
                        }
                    }
                }
            },
            //简历修改
            jianli_switch: function() {
                $('#jianli_switch').click(function() {
                    $.ajax({
                        type: 'POST',
                        url: '/admin/index/jianli_switch',
                        success: function(response) {
                            if (response == 1) {
                                $('#jianli_switch').html('关闭简历');
                            } else if (response == 0) {
                                $('#jianli_switch').html('开启简历');
                            } else {
                                alert('简历操作失败,请查看后台代码是否有问题');
                            }
                        }
                    })
                })
            },
            Expiredreminder: function() {
                $.ajax({
                    type: "POST",
                    url: '/admin/index/lastLogin/',
                    success: function(res) {
                        //过期时间
                        var endtime = res * 1000 + 3600 * 1000;
                        var timer = setInterval(function() {
                            var now = Date.now();
                            if (endtime - now < 21600 * 1000) {
                                //弹出提醒还剩下10分钟窗口
                                page.countdown(timer, now, endtime);
                            } else {
                                //如果还有很多则使用计时器进行计时
                                now += 1000;
                                if (endtime - now < 600 * 1000) {
                                    page.countdown(timer, now, endtime);
                                }
                            }
                        })
                    }
                })
            },
            //因为时间到期转向登录界面无法清除session（登录界面不检测session否则会进行报错），所以通过ajax来让TP删除session
            empty_session: function() {
                $.ajax({
                    type: 'post',
                    url: '/admin/index/emptySession',
                    success: function(res) {}
                })
            },
            //时间倒数封装
            countdown: function(timer, now, endtime) {
                var t = parseInt((endtime - now) / 1000);
                var m = parseInt(t / 60);
                var s = t % 60 < 10 && t % 60 >= 0 ? "0" + t % 60 : t % 60;
                if (m <= 0 && s <= 0) {
                    alert('登录时间到期！');
                    clearInterval(timer);
                    page.empty_session();
                    window.location.href = "/admin/login";
                } else {
                    $('.Expiredreminder').text('登录剩余时间还剩' + m + ':' + s + '，请注意及时保存内容');
                }
            }
        }
        //页面效果初始化
    page.run();


    //所有需要ajax进行修改的地址
    var ajaxUrl = {
        //日记相关
        diary_modify: '/admin/diary/modify_diary/id/' + $('#diaryId').val(),
        diary_del: '/admin/diary/del/id/',
        diary_create: '/admin/diary/add_diary',
        // diary_search:'/admin/diary/search',
        //个人信息相关
        person_modify: '/admin/modify_person',
        person_logo: '/admin/person_logo',
        //工作相关
        jianli_modify: '/admin/jianli/modify',
        work_modify: '/admin/jianli/work_modify',
        //幻灯片相关
        Vflash_upload: '/admin/flash/addFlash/type/0',
        Hflash_upload: '/admin/flash/addFlash/type/1',
        flash_modify: '/admin/flash/modifyFlash',
        flash_del: '/admin/flash/delflash/id/',
        //文章相关
        article_modify: '/admin/modify_article/' + $('#articleId').val(),
        article_del: '/admin/del/',
        // article_search:'/admin/search',
        article_status: '/admin/status/',
        article_create: '/admin/add_article'
    };


    //文章
    var article = {
        run: function() {
            article.initial();
            article.add();
            article.del();
            article.search();
            article.modify();
        },
        initial: function() {
            $('.status').click(function() {
                    status_change($(this));
                })
                //ajax分页，BUG:刷新页面会回到第一页,看个人需求
                // $('.pagination a').click(function (event) {
                // 	event.preventDefault();
                //              var UpUrl =$(this).attr('href');
                //              $.ajax({
                //              	type:'POST',
                //              	url:UpUrl,
                //              	success:function (response) {
                //               	$('.table').html($(response).find('table').html());
                //               	$('.pagination').html($(response).find('.pagination').html());
                //               	$('.pagination a').on('click',ajaxpage());
                //               },
                //            error:function(){
                // error(XMLHttpRequest,status)
                //  }
                //        })
                //    })

            if (window.location.pathname == '/admin/search') {
                that = $(".list ul a:contains('网站文章')")
                that.addClass('active').parent().addClass('active').children('.tree-menu').show();
            }
        },
        add: function() {
            $("#add_article").validate({
                submitHandler: function(form) {
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl.article_create,
                        data: $('#add_article').serialize(),
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                alert('文章添加成功');
                                location.href = '/admin/article'
                            } else if (response == 'validate') {
                                $('#info').html('表单内容验证未通过').stop().fadeOut().fadeIn();
                            } else {
                                $('#info').html('已有同样标题文章存在！').stop().fadeOut().fadeIn();
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    });
                }
            })
        },
        del: function() {
            $('.del-article').click(function() {
                if (confirm("是否删除这篇文章")) {
                    var that = $(this);
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl.article_del + that.attr('title'),
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                alert('删除文章成功');
                                that.parent().parent().remove();
                            } else {
                                alert('删除文章失败');
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    })
                }
            })
        },
        search: function() {
            $('#search_article').click(function() {
                // 	$.ajax({
                // 		type: "GET",
                // 		url:ajaxUrl.article_search,
                // 		data:$('#article_result').serialize(),
                // 		success:function(response){
                // 			var text;
                // 			$('.table').children('tbody').remove();
                // 			if(response=='level'){
                //                		alert('账号没有权限修改此功能');
                //                }else{
                // 				$('.table').children('tbody').remove();
                // 				$('.pagination').remove();
                // 				for(var i=0;i<response.length;i++){
                // 					var type  = response[i].type == 1 ?'HTML5':response[i].type == 2?'CSS3':'Javascript';
                // 					var status =response[i].type ?'隐藏':'显示';
                // 					text += '<tr><td>'+response[i].title+'</td><td>'+type+'</td><td>'+response[i].intro+'</td><td>'+response[i].create_time+'</td><td>'+response[i].update_time+'</td><td><a href="/admin/modify/'+response[i].id+'">编辑</a> | <a class="status" href="javascript:" title="'+response[i].id+'">'+status+'</a> | <a class="del_article" href="javascript:" title="'+response[i].id+'">删除</a></td></tr>}';
                // 				}
                // 			}
                // 			$('.table').append(text);
                // 			$('.status').on('click',function(){
                // 				status_change($(this));
                // 			})
                // 		}
                // 	})
                // })
                //定义搜索之后列表active内容
            })
        },
        modify: function() {
            $("#modify_article").validate({
                submitHandler: function(form) {
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl.article_modify,
                        data: $('#modify_article').serialize(),
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                alert('文章修改成功');
                                location.reload(true);
                            } else if (response == 'warn') {
                                $('#info').html('没有任何信息修改哦！').stop().fadeOut().fadeIn();
                            } else if (response == 'validate') {
                                $('#info').html('表单内容验证未通过').stop().fadeOut().fadeIn();
                            } else {
                                $('#info').html('已有同样标题文章存在！').stop().fadeOut().fadeIn();
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    });
                }
            });
        }
    };
    article.run();

    //日记
    var diary = {
        run: function() {
            diary.add();
            diary.del();
            diary.initial();
            // diary.search();
            diary.modify();
        },
        initial: function() {
            // $('.pagination a').click(function (event) {
            // 	event.preventDefault();
            //  var UpUrl =$(this).attr('href');
            //ajax分页，BUG:刷新页面会回到第一页,看个人需求
            //       $.ajax({
            //       	type:'POST',
            //       	url:UpUrl,
            //       	success:function (response) {
            //        	$('.table').html($(response).find('table').html());
            //        	$('.pagination').html($(response).find('.pagination').html());
            //        	$('.pagination a').on('click',ajaxpage());
            //        	return false;
            //        },
            //     error:function(){
            // 	error(XMLHttpRequest,status)
            // }
            // })
            // })
            if (window.location.pathname == '/admin/diary/search') {
                $(".list ul a[href='/admin/diary/index.html']").parent().addClass('active');
            }
        },
        add: function() {
            $("#add_diary").validate({
                submitHandler: function(form) {
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl.diary_create,
                        data: $('#add_diary').serialize(),
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                alert('日记添加成功');
                                location.href = '/admin/diary';
                            } else if (response == 'validate') {
                                $('#info').html('表单内容验证未通过').stop().fadeOut().fadeIn();
                            } else if (response == 'error') {
                                alert('已有同样内容存在！');
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    });
                }
            });
        },
        del: function() {
            $('.del_diary').click(function() {
                if (confirm("是否删除这篇日记")) {
                    var that = $(this);
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl.diary_del + that.attr('title'),
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                alert('删除日记成功');
                                that.parent().parent().remove();
                            } else {
                                alert('删除日记失败');
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    })
                }
            })
        },
        // search:function(){
        // 	$('#search_diary').click(function(){
        // 		$.ajax({
        // 			type: "POST",
        // 			url:ajaxUrl.diary_search,
        // 			data:$('#diary_result').serialize(),
        // 			success:function(response){
        // 				var text;
        // 				$('.table').children('tbody').remove();
        // 				if(response=='level'){
        //                 		alert('账号没有权限修改此功能');
        //                 }else{
        // 					for(var i=0;i<response.length;i++){
        // 						text += '<tr><td>'+response[i].title+'</td><td>'+response[i].content+'</td><td>'+response[i].create_time+'</td><td><a href="/admin/diary/modify/id/'+response[i].id+'">编辑</a> | <a class="del_article" href="javascript:" title="'+response[i].id+'">删除</a></td></tr>}';
        // 				}
        // 				}
        // 				$('.table').append(text);
        // 			}
        // 		})
        // 	})
        // },
        modify: function() {
            $("#modify-diary").validate({
                submitHandler: function(form) {
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl.diary_modify,
                        data: $('#modify-diary').serialize(),
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                alert('日记修改成功');
                                location.href = '/admin/diary';
                            } else if (response == 'error') {
                                $('#info').html('已有同样标题日记存在！').stop().fadeOut().fadeIn();
                            } else if (response == 'validate') {
                                $('#info').html('表单内容验证未通过').stop().fadeOut().fadeIn();
                            } else {
                                $('#info').html('没有任何修改哦!').stop().fadeOut().fadeIn();
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    });
                }
            });
        }
    };
    diary.run();


    //幻灯片
    var flash = {
        run: function() {
            flash.initial();
            flash.addH();
            flash.addV();
            flash.addH();
            flash.modifyH();
            flash.modifyV();
            flash.del();
        },
        initial: function() {
            $('#Tab li:eq(0) a').tab('show');
            $('#addFlash').on('hidden.bs.modal', function(e) {
                location.reload(true);
            })
        },
        addH: function() {
            $('#add-Hflash').click(function() {
                if ($('#Hflash-table tbody tr').length >= 10) {
                    alert('幻灯片最多只能添加10张');
                    return false;
                }
                $("#upload-flash").fileinput({
                    language: 'zh',
                    uploadUrl: ajaxUrl.Hflash_upload,
                    allowedFileExtensions: ['jpg', 'gif', 'png'],
                });
            })
        },
        addV: function() {
            $('#add-Vflash').click(function() {
                if ($('#Vflash-table tbody tr').length >= 10) {
                    alert('幻灯片最多只能添加10张');
                    return false;
                }
                $("#upload-flash").fileinput({
                    language: 'zh',
                    uploadUrl: ajaxUrl.Vflash_upload,
                    allowedFileExtensions: ['jpg', 'gif', 'png']
                });
            })
        },
        modifyH: function() {
            $("#Hflash-modify").validate({
                submitHandler: function(form) {
                    $.ajax({
                        data: $('#Hflash-modify').serialize(),
                        url: ajaxUrl.flash_modify,
                        beforeSend: function() {
                            var arr = [];
                            var lianxu;
                            var j = 0;
                            var count;
                            var leng = $("#Hflash-table input[name='order[]']").length
                            for (var i = 0; i < leng; i++) {
                                arr.push(parseInt($("#Hflash-table input[name='order[]']").eq(i).val()));
                                //获取值，并且对值进行从小到大排序
                                arr.sort();
                                //将数组中的值进行相加,因为不兼容IE所以采用reduce方法
                                var count = arr.reduce(function(prev, cur) {
                                    return prev + cur;
                                });
                            }
                            //判断值是否连续,开始值是否为1
                            if ((arr[0] + arr[leng - 1]) * leng / 2 !== count || arr[0] !== 1) {
                                alert('排序必须从1开始的连续数字');
                                return false;
                            }
                        },
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                alert('排序修改成功');
                                location.href = '/admin/flash';
                            } else if (response == 'error') {
                                $('#info').html('没有任何信息修改哦！').stop().fadeOut().fadeIn();
                            } else {
                                $('#info').fadeOut().html('修改错误,此问题涉及到数据库,需要进行修复').stop().fadeOut().fadeIn();
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    });
                }
            });
        },
        modifyV: function() {
            $("#Vflash-modify").validate({
                submitHandler: function(form) {
                    $.ajax({
                        data: $('#Vflash-modify').serialize(),
                        url: ajaxUrl.flash_modify,
                        beforeSend: function() {
                            var arr = [];
                            var lianxu;
                            var j = 0;
                            var count;
                            var leng = $("#Vflash-table input[name='order[]']").length
                            for (var i = 0; i < leng; i++) {
                                arr.push(parseInt($("#Vflash-table input[name='order[]']").eq(i).val()));
                                //获取值，并且对值进行从小到大排序
                                arr.sort();
                                //将数组中的值进行相加,因为不兼容IE所以采用reduce方法
                                var count = arr.reduce(function(prev, cur) {
                                    return prev + cur;
                                });
                            }
                            //判断值是否连续,开始值是否为1
                            if ((arr[0] + arr[leng - 1]) * leng / 2 !== count || arr[0] !== 1) {
                                alert('排序必须从1开始的连续数字');
                                return false;
                            }
                        },
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                alert('排序修改成功');
                                location.href = '/admin/flash';
                            } else if (response == 'error') {
                                $('#info2').html('没有任何信息修改哦！').stop().fadeOut().fadeIn();
                            } else {
                                $('#info2').fadeOut().html('修改错误,此问题涉及到数据库,需要进行修复').stop().fadeOut().fadeIn();
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    });
                }
            });
        },
        del: function() {
            $('.del-flash').click(function() {
                if (confirm("是否删除这张幻灯片")) {
                    var that = $(this);
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl.flash_del + that.attr('title'),
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                alert('删除幻灯片成功');
                                location.href = '/admin/flash';
                            } else {
                                alert('删除幻灯片失败');
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    })
                }
            })
        }
    };
    flash.run()


    //简历
    var jianli = {
        run: function() {
            jianli.initial();
            jianli.addWork();
            jianli.delWork();
            jianli.modifyWork();
            jianli.modifyJL();
        },
        count: $('.work .panel-body').length,
        initial: function() {
            var count = jianli.count;
            for (var i = 1; i <= count; i++) {
                (function(j) {
                    $('.work .panel-body .start' + j).flatpickr({
                        'static': true,
                        maxDate: 'today',
                        onChange: function(dateObj, dateStr, instance) {
                            $('.work .panel-body .end' + j).flatpickr({
                                'static': true,
                                minDate: dateStr,
                                maxDate: 'today',
                            });
                        }
                    });
                    $('.work .panel-body .end' + j).flatpickr({
                        'static': true,
                        minDate: $('.work .panel-body .start' + j).val(),
                        maxDate: 'today',
                    });
                })(i)
            }
        },
        addWork: function() {
            //给id的自增值
            var j = $('.panel-body .id').last().val();
            $('#addWork').click(function() {
                var i = $('.id').length; //获取id的长度
                var date = new Date();
                var Y = date.getFullYear();
                var M = date.getMonth() + 1;
                var D = date.getDate();
                M = M < 10 ? '0' + M : M;
                D = D < 10 ? '0' + D : D;
                var today = Y + '-' + M + '-' + D;
                //判断履历数量
                if ($('.panel-body .id').length >= 5) {
                    alert("履历数不能超过5，请删除之前的履历");
                    return false;
                }
                //如果离职时间为今天则不能添加新履历
                if ($('.end' + i).val() == today) {
                    alert("离职时间为今天，不能添加新履历");
                    return false;
                }
                i++;
                j++;
                var text = '<div class="panel-body"><span class="num">' + i + '.</span><a class="delWork" href="javascript:">删除</a><i class="clearfix"></i><input class="id" type="hidden" value="' + j + '" name="id[]"><div class="form-group time"><label for="start">工作时间</label><input name="start[]" class="start' + i + ' form-control" type="text"></div> - <div class="form-group time"><label for="end">离职时间</label><input name="end[]" class="end' + i + ' form-control" type="text"></div><div class="form-group"><label for="company">公司名称</label><input name="company[]" id="company' + i + '" class="form-control" type="text" minlength="5" maxlength="12" required></div><div class="form-group"><label for="job">岗位</label><input name="job[]" id="job' + i + '" class="form-control" type="text" minlength="2" maxlength="10" required></div><div class="form-group"><label for="intro">工作概况</label><textarea name="intro[]" id="intro' + i + '" class="form-control" rows="3" required minlength="10" maxlength="200"></textarea></div></div>';
                $('#save').before(text)
                $('.start' + i).flatpickr({
                    'static': true,
                    maxDate: 'today',
                    onChange: function(dateObj, dateStr, instance) {
                        $('.end' + i).flatpickr({ 'static': true, minDate: dateStr, maxDate: 'today' })
                    }
                });
                $('.end' + i).flatpickr({ 'static': true, maxDate: 'today' });
                $('.delWork').off('click');
                jianli.delWork();
            })
        },
        delWork: function() {
            $('.delWork').on('click', function() {
                var count = $('.work .panel-body').length;
                var index = $(this).parent().index();
                if ($('.delWork').length == 1) {
                    alert('至少需要一个工作履历');
                    return false;
                }
                if (index + 1 != count) {
                    for (var i = index; i < count; i++) {
                        $('.work .panel-body').eq(i).find('.start' + (i + 1)).attr('class', 'start' + i + ' form-control flatpickr-input')
                        $('.work .panel-body').eq(i).find('.end' + (i + 1)).attr('class', 'end' + i + ' form-control flatpickr-input')
                    }
                }
                $(this).parent().remove();
                for (var i = 0; i < $('.num').length; i++) {
                    $('.num').eq(i).text(i + 1 + '.');
                }
            });
        },
        modifyWork: function() {
            $("#work_modify").validate({
                submitHandler: function(form) {
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl.work_modify,
                        data: $('#work_modify').serialize(),
                        beforeSend: function() {
                            for (var i = 0; i < $('#work_modify .panel-body').length; i++) {
                                var start = $("input[name='start[]']");
                                var end = $("input[name='end[]']");
                                var startT = new Date(start.eq(i + 1).val()).getTime() / 1000;
                                var endT = new Date(end.eq(i).val()).getTime() / 1000;
                                if (!start.eq(i).val() || !end.eq(i).val()) {
                                    alert('第' + (i + 1) + '份履历时间没选择');
                                    return false;
                                } else if (startT <= endT) {
                                    alert('第' + (i + 2) + '份履历时间与上一份履历时间重合');
                                    return false;
                                }
                            }
                        },
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                $('#info1').fadeOut().html('修改成功').stop().fadeOut().fadeIn();
                            } else if (response == 'warn') {
                                $('#info1').fadeOut().html('没有任何信息修改哦！').stop().fadeOut().fadeIn()
                            } else if (response == 'validate') {
                                $('#info').html('表单内容验证未通过').stop().fadeOut().fadeIn();
                            } else {
                                $('#info1').fadeOut().html('修改错误,此问题涉及到数据库,需要进行修复').stop().fadeIn();
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    });
                }
            });
        },
        modifyJL: function() {
            $("#JL").validate({
                submitHandler: function(form) {
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl.jianli_modify,
                        data: $('#JL').serialize(),
                        success: function(response) {
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                $('#info').html('修改成功').stop().fadeOut().fadeIn()
                            } else if (response == 'validate') {
                                $('#info').html('表单内容验证未通过').stop().fadeOut().fadeIn();
                            } else if (response == 'warn') {
                                $('#info').html('没有任何信息修改哦！').stop().fadeOut().fadeIn()
                            } else {
                                $('#info').html('修改错误,此问题涉及到数据库,需要进行修复').stop().fadeOut().fadeIn()
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    });
                }
            });
        }

    }
    jianli.run();


    //个人
    var person = {
        run: function() {
            person.initial();
            person.modify();
        },
        initial: function() {
            $('#upload').on('fileuploaded', function(event, data, previewId, index) {
                if (res == 'error') {
                    $('#info').html('logo修改成功');
                }
            });
        },
        modify: function() {
            $("#person_modify").validate({
                submitHandler: function(form) {
                    $.ajax({
                        data: $('#person_modify').serialize(),
                        url: ajaxUrl.person_modify,
                        beforeSend: function() {
                            if ($('#upload').val() != "") {
                                $('#upload').fileinput('upload');
                            }
                        },
                        success: function(response) {
                            res = response;
                            if (response == 'level') {
                                alert('账号没有权限修改此功能');
                            } else if (response == 'success') {
                                $('#info').html('信息修改成功').stop().stop().fadeOut().fadeIn();
                            } else if (response == 'validate') {
                                $('#info').html('表单内容验证未通过').stop().fadeOut().fadeIn();
                            } else {
                                $('#info').html('没有任何信息修改哦！').stop().fadeOut().fadeIn();
                            }
                        },
                        error: function() {
                            error(XMLHttpRequest, status)
                        }
                    });
                }
            });
        }
    }
    person.run();


    /*封装方法*/
    //文章显示或隐藏
    function status_change(obj) {
        var that = obj;
        $.ajax({
            type: "POST",
            url: ajaxUrl.article_status + that.attr('title'),
            success: function(response) {
                if (response == 'level') {
                    alert('账号没有权限修改此功能');
                } else if (response == 'error') {
                    alert('修改失败');
                } else if (response == '1') {
                    $(that).text('隐藏');
                } else {
                    $(that).text('显示');
                }
            }
        })
    }

    function error(XMLHttpRequest, status) {
        alert('返回数据：' + XMLHttpRequest + '错误信息：' + status);
    }
})