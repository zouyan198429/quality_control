<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>教育云助手</title>
	<link rel="stylesheet" href="{{asset('staticweb/css/wnui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('staticweb/css/skin.css')}}" media="all">
  	<link rel="stylesheet" href="{{asset('staticweb/css/mb001.css')}}" media="all">
    <script type="text/javascript" src="{{asset('staticweb/js/jquery.js')}}"></script>
  	<script type="text/javascript" src="{{asset('staticweb/js/styleswitch.js')}}"></script>
  	<script type="text/javascript" src="{{asset('staticweb/js/html2canvas.js')}}"></script>
    <script src="{{asset('staticweb/js/youziku.justtime.js')}}"></script>

    <script type="text/JavaScript">
        var dragresize = new DragResize('dragresize',
        { minWidth: 5, minHeight: 5, minLeft: -1200, minTop: -1200, maxLeft: 11000, maxTop: 10000 });
        dragresize.isElement = function(elm)
        {
        if (elm.className && elm.className.indexOf('weimoban') > -1) return true;
        };
        dragresize.isHandle = function(elm)
        {
        if (elm.className && elm.className.indexOf('drsMoveHandle') > -1) return true;
        };
        dragresize.ondragfocus = function() { };
        dragresize.ondragstart = function(isResize) { };
        dragresize.ondragmove = function(isResize) { };
        dragresize.ondragend = function(isResize) { };
        dragresize.ondragblur = function() { };
        dragresize.apply(document);

    </script>

    <style type="text/css">
            .weimoban {position: absolute; }
            .drsMoveHandle { cursor: move; background: none;}
            .drsMoveHandle:hover { display:block; opacity:0.2; background: #000; }
    </style>

    <script language="javascript">
        function openwin()//跳出弹窗
            {
              window.open("/index.php/Moban/pc.html", "", "location=no, menubar=no, left=100 ,top=70,height=520, width=800" );
            }
        function doZoom(size)//字体大小调整
            {
            document.getElementById(dfr).style.fontSize=size+'px';
            }
        function test(na)// na里面是事件传递过来的id
            {
            var script= $("#"+na+"").attr("name");//然后根据id获得name 值
             window.yut = script;  //全局化 neme值；
             window.dfr = na;   //全局化 id值；
            }
        function srtty(frty)//字体控制
            {
             changefont(yut, frty);
            }
        function setColor(ddr)//文字颜色控制
            {
            document.getElementById(dfr).style.color=ddr;
            }
        function print()
        {
            $(function() {
                    html2canvas(document.getElementById("picbox"), {
                            allowTaint: true,
                            taintTest: false,
                        onrendered: function(canvas) {
                        var DataURL= canvas.toDataURL("image/png");

                        var saveFile = function(data, filename){
                            var save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
                            save_link.href = data;
                            save_link.download = filename;

                            var event = document.createEvent('MouseEvents');
                            event.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                            save_link.dispatchEvent(event);
                        };
                        saveFile(DataURL,"weitumoban.png");
                        }
                    });
                });
        }
        function edit(element){
            if($("#"+dfr+"").hasClass('drsMoveHandle')){
                $("#"+dfr+"").removeClass("drsMoveHandle");
            } else{
             var obj = document.getElementById(dfr);
                obj.className += ' drsMoveHandle';
            }
        }
        //字体大小写 增加 或者 减小
      $(function() {
        $("span").click(function() {
            var thisEle = $("#" + dfr + "").css("font-size");
            var textFontSize = parseFloat(thisEle, 10);
            var unit = thisEle.slice(-2); //获取单位
            var cName = $(this).attr("class");
            if (cName == "bigger") {
                if (textFontSize <= 160) {
                    textFontSize += 2;
                }
            } else if (cName == "smaller") {
                if (textFontSize >= 12) {
                    textFontSize -= 2;
                }
            }
            $("#" + dfr + "").css("font-size", textFontSize + unit);
        });
      })

      // 获得默认插入学生对象
      function getDefaultSeledObj(){
        return $('#txt3');
      }


        $(document).ready(function(){

            // 显示/隐藏删除
            $(".float_div").hover(function(){
                console.log('移入');
                let operateObj = $(this);
                operateObj.find('.del').show();
            },function(){
                console.log('移出');
                let operateObj = $(this);
                operateObj.find('.del').hide();
            });

            // 移除对象
            $(".del").click(function(){
                let del_obj = $(this);
                 del_obj.closest('.float_div').remove();
            });



            var SELECTED_OBJ = getDefaultSeledObj();// 插入学生名字对象--全局

            $(".selected_type").click(function(){
                let click_obj = $(this);
                console.log(click_obj);
                // 1 代表满分作业  2 代表 需要补做
                let type_num = click_obj.attr("data-type-num");
                console.log('type_num=', type_num);
                if(type_num == 1 || type_num == "1") {
                    SELECTED_OBJ = $('#txt1');
                }else if (type_num == 2 || type_num == "2"){
                    SELECTED_OBJ = $('#txt2');
                }else if (type_num == 3 || type_num == "3"){
                    SELECTED_OBJ = $('#txt3');
                }else if (type_num == 4 || type_num == "4"){
                    SELECTED_OBJ = $('#txt4');
                }else if (type_num == 5 || type_num == "5"){
                    SELECTED_OBJ = $('#txt5');
                }else if (type_num == 6 || type_num == "6"){
                    SELECTED_OBJ = $('#txt6');
                }else{
                    SELECTED_OBJ = getDefaultSeledObj();
                }

            });
            //增加文字
           $("#txtadd").click(function(){
            var kkk=parseInt(Math.random()*(500-100+1)+100);
            var nba=Math.floor(Math.random()*500); //0-500
            var nbb=Math.floor(Math.random()*500);//0-500
            var nam ="txt" +kkk*11;
            var name ="text" +kkk*11;
            var naal=nba+"px"; // 左边距
            var naar=nbb+"px";  // 高度
            //document.write(nam);
            $("#picbox").append("<div class='drsMoveHandle weimoban selected_type float_div'  id='" +nam+ "' name='" +name+ "' onclick='test(this.id)' data-type-num=2 style='position: absolute; left:" +naal+ "; top:" +naar+ "' ondblclick='edit(this.id)'><span contenteditable='true' style=''>教育云助手</span> <div class='del' ><img src='images/del.svg' ></div></div>");
           });

            //增加文字
           $(".class_name").click(function(){
            let parentObj = SELECTED_OBJ;// $('#txt4');
            let class_obj = $(this);
            let class_name = class_obj.html();
            console.log('class_name=', class_name);
            var kkk=parseInt(Math.random()*(500-100+1)+100);
            var nba=Math.floor(Math.random()*500); //0-500
            var nbb=Math.floor(Math.random()*500);//0-500
            var nam ="txt" +kkk*11;
            var name ="text" +kkk*11;
            var naal=nba+"px"; // 左边距
            var naar=nbb+"px";  // 高度
            //document.write(nam);
            parentObj.append(" <span contenteditable='true'>" +class_name+ "</span>");
            // $("#picbox").append("<div class='drsMoveHandle weimoban txt'  id='" +nam+ "' name='" +name+ "' onclick='test(this.id)'  style='left:" +naal+ "; top:" +naar+ "' ondblclick='edit(this.id)'><span contenteditable='true' style=''>" + class_name + "</span></div>");
           });
        });

        $(document).ready(function(){
            //增加图片
           $("#picadd").click(function(){
            var kkk=parseInt(Math.random()*(500-100+1)+100);
            var nba=Math.floor(Math.random()*500); //0-500
            var nbb=Math.floor(Math.random()*500);//0-500
            var nam ="pic" +kkk*11;
            var name ="text" +kkk*11;
            var naal=nba+"px"; // 左边距
            var naar=nbb+"px";  // 高度
            //document.write(nam);
            $("#picbox").append("<div class='drsMoveHandle weimoban pic'  id='" +nam+ "' name='" +name+ "' style='left:" +naal+ "; top:" +naar+ " ;z-index=600; width=100px; '  ><img src='Mobanimg/m0245/images/pic01.jpg' width=100%  /></div>");
           });
        });


             //   <div class="weimoban drsMoveHandle" style="left:0px; top:0px;" id="pic01"><img src="Mobanimg/m0245/images/pic01.jpg"  ondblclick="openwind()" id="ewp" /></div>


        /**
         * function=======显示二维码
         */
        function showCode() {
            var picboxInfo  = $('#jietu').html();
            var getMid      = $('#mobanId').val();
            $.ajax({
                type: "POST",
                url: "/index.php/Moban/imgCode",
                dataType:"json",
                data: "content="+picboxInfo+"&mid="+getMid,
                success: function(data){
                    if(data.status=="0"){
                        $("#htmlCode").attr('src',"/"+data.data);
                        $("#lookCode").css({'display':'block'});
                        $("#jietu").css({'display':'none'});
                    }else{
                        alert(data.data);
                    }
                }
            });
        }
        /**
         * function======隐藏二维码
         */
        function hideCode() {
            $("#lookCode").css({'display':'none'});
            $("#jietu").css({'display':'block'});
        }
    </script>

    <script>
    // 字体修改 隐藏显示
    $(function(){
        $('#lol').click(function(){　　//点击a标签
        if($('#family').is(':hidden')){　　//如果当前隐藏
        $('#family').show();　　//那么就显示div
        }else{　　//否则
        $('#family').hide();　　//就隐藏div
        }
        })
    })
    </script>


</head>
<body style="background:#f1f3f3;">

<div class="header">
	<div class="header-left fl">
		<a href="template.html">返回</a>
	</div>
	<div class="header-right fr">
		<img src="images/icon-mess.png"  alt="">
		<img src="images/icon-user-b.png"  alt="">
		<a href="login.html" class="member">18888384423</a>
		<a href="login.html" class="quit">退出</a>
	</div>
</div>
<div class="k50"></div>
<div class="card-edit-top">
	<button onclick="print() " class="btn" >生成卡片</button>
	<a href="" class="btn" >保存</a>
</div>
<div class="k50"></div>
<div class="card-left">
    <button class="btn-sc fl" id="txtadd">追加文本</button>
    <button class="btn-sc fl" onClick="logo()">插入图片</button>


    <div class="c" ></div>
    <div id="fontsize">
            <div class="hd">文字大小</div>
            <span class="smaller" title="缩小" >-</span>
            <span onclick="javascript:doZoom(16)">·</span>
            <span onclick="javascript:doZoom(24)">·</span>
            <span onclick="javascript:doZoom(36)">·</span>
            <span onclick="javascript:doZoom(48)">·</span>
            <span onclick="javascript:doZoom(72)">·</span>
            <span onclick="javascript:doZoom(120)">·</span>
            <span class="bigger" title="放大" >+</span>
    </div>
    <div id="fcolors">
            <div class="hd">文字颜色</div>
            <span  style="background:#cc0000;" onclick="javascript:setColor('#cc0000')"  ></span>
            <span  style="background:#FF0000;" onclick="javascript:setColor('#FF0000')"  ></span>
            <span  style="background:#FF6600;" onclick="javascript:setColor('#FF6600')"  ></span>
            <span  style="background:#FFA921;" onclick="javascript:setColor('#FFA921')"  ></span>
            <span  style="background:#FFF45C;" onclick="javascript:setColor('#FFF45C')"  ></span>
            <br />
            <span  style="background:#D7014B;" onclick="javascript:setColor('#D7014B')"  ></span>
            <span  style="background:#FF1D6B;" onclick="javascript:setColor('#FF1D6B')"  ></span>
            <span  style="background:#FF4DA9;" onclick="javascript:setColor('#FF4DA9')"  ></span>
            <span  style="background:#FF80C5;" onclick="javascript:setColor('#FF80C5')"  ></span>
            <span  style="background:#FEB4CD;" onclick="javascript:setColor('#FEB4CD')"  ></span>
            <br />
            <span  style="background:#129428;" onclick="javascript:setColor('#129428')"  ></span>
            <span  style="background:#6FE012;" onclick="javascript:setColor('#6FE012')"  ></span>
            <span  style="background:#87C943;" onclick="javascript:setColor('#87C943')"  ></span>
            <span  style="background:#B0F246;" onclick="javascript:setColor('#B0F246')"  ></span>
            <span  style="background:#DEF66E;" onclick="javascript:setColor('#DEF66E')"  ></span>
            <br />
            <span  style="background:#01589D;" onclick="javascript:setColor('#01589D')"   ></span>
            <span  style="background:#2D7DDB;" onclick="javascript:setColor('#2D7DDB')"  ></span>
            <span  style="background:#5FA9FE;" onclick="javascript:setColor('#5FA9FE')"  ></span>
            <span  style="background:#3ABCFF;" onclick="javascript:setColor('#3ABCFF')"  ></span>
            <span  style="background:#71D9FF;" onclick="javascript:setColor('#71D9FF')"  ></span>
            <br />
            <span  style="background:#7B0C00;" onclick="javascript:setColor('#7B0C00')"  ></span>
            <span  style="background:#C63F46;" onclick="javascript:setColor('#C63F46')"  ></span>
            <span  style="background:#AB1EA8;" onclick="javascript:setColor('#AB1EA8')"  ></span>
            <span  style="background:#049D7F;" onclick="javascript:setColor('#049D7F')"  ></span>
            <span  style="background:#7D65E9;" onclick="javascript:setColor('#7D65E9')"  ></span>
            <br />
            <span  style="background:#000000;" onclick="javascript:setColor('#000000')"  ></span>
            <span  style="background:#333333;" onclick="javascript:setColor('#333333')"  ></span>
            <span  style="background:#666666;" onclick="javascript:setColor('#666666')"  ></span>
            <span  style="background:#CCCCCC;" onclick="javascript:setColor('#CCCCCC')"  ></span>
            <span  style="background:#ffffff;" onclick="javascript:setColor('#ffffff')"  ></span>
        </div>
        <div id="fontfamily">

        <a href="javascript:void(0);" id="lol"><div class="hd">更改字体</div></a>
        <div id="family"  >
            <a  id="ft01" onclick="srtty('e2c723dfa3ae487a8a027283712f74b8')" title="思源黑体"></a>
            <a  id="ft02" onclick="srtty('456ba0b92b8a43b3a29109cbc4b26335')" title="苹方超细黑详"></a>
            <a  id="ft03" onclick="srtty('4b69f0dfece84d88bc9eccaa021a44bb')" title="文鼎特圆简"></a>
            <a  id="ft04" onclick="srtty('010c0b918e614361bacd3483236930f9')" title="四通利方细圆体简"></a>
            <a  id="ft05" onclick="srtty('77854ced42574ccf80026de343d72f63')" title="迷你简书魏"></a>
            <a  id="ft06" onclick="srtty('9aec58c38ea44c899e19ed1a3c998628')" title="中国龙豪隶书"></a>
            <a  id="ft07" onclick="srtty('f9b687053bc54d02acb2077a73b0039e')" title="字悦毛笔隶书"></a>
            <a  id="ft08" onclick="srtty('a74e7dd785be4646879d7a57b4d3b04f')" title="思源宋体"></a>
            <a  id="ft09" onclick="srtty('7e86f18f31fb46dfbae888e74c15e23c')" title="思源宋体 SemiBold"></a>
            <a  id="ft10" onclick="srtty('3cf0cd2263bc4020af21cabee0224a18')" title="思源宋体 Bold"></a>
            <a  id="ft11" onclick="srtty('290573e5e66c469e9af05426416b59a8')" title="思源宋体 Heavy"></a>
            <a  id="ft12" onclick="srtty('e4d13e9693064da993aea0b5d0afab59')" title="DF极太超明朝体"></a>
            <a  id="ft13" onclick="srtty('77e64b8e5f8f4e8ab75bcb768c427230')" title="田英章钢笔行书简体"></a>
            <a  id="ft14" onclick="srtty('21132dc7691148e5bec1b1b3ef4dc226')" title="默陌信笺手写体"></a>
            <a  id="ft15" onclick="srtty('bdc219feed9c4864874690cc785b81c6')" title="王汉宗中楷体简"></a>
            <a  id="ft16" onclick="srtty('0134bb3bbfde49ff92d19a7de0a9d00c')" title="迷你简硬笔楷书"></a>
            <a  id="ft17" onclick="srtty('5d9919bec720489081dde2222167d8dc')" title="李林哥特体"></a>
            <a  id="ft18" onclick="srtty('55ca0769b5e74583823eeab5b971ddc4')" title="字体中国锐博体"></a>
            <a  id="ft19" onclick="srtty('dbb38b95c3454128b4cb9f3dec564585')" title="海派腔调禅粗黑简2.0"></a>
            <a  id="ft20" onclick="srtty('f1295471523f4604b1d2a7b3b065c24f')" title="Aa方萌"></a>
            <a  id="ft21" onclick="srtty('7a3245793ed34c0fa5e020b4eb030df9')" title="庞门正道标题体"></a>
            <a  id="ft22" onclick="srtty('f85086a1b56245988c04a19d8c8e7431')" title="H-新雅兰-Bold"></a>
            <a  id="ft23" onclick="srtty('6ae12238670a44f8a3d7f02ed869c40d')" title="经典特黑简"></a>
            <a  id="ft24" onclick="srtty('dee44ebad8a94da6b0008862e1085f32')" title="造字工房劲黑"></a>
            <a  id="ft25" onclick="srtty('d2848bcf209a48a4966ec618e5e03af4')" title="锐字工房荣光粗黑简"></a>
            <a  id="ft26" onclick="srtty('94f3bc7f6f794602b8849a7e6dc06814')" title="锐字逼格青春粗黑体简"></a>
            <a  id="ft27" onclick="srtty('6b63ae37ff1341608fcf17221cb520aa')" title="锐字云字库综艺体"></a>
            <a  id="ft28" onclick="srtty('5670b12a278c4422a7c0e120a4624621')" title="经典粗仿黑"></a>
            <a  id="ft29" onclick="srtty('c49381f286d1446192e6dbc1df706cba')" title="锐字锐线梦想黑简1.0"></a>
            <a  id="ft30" onclick="srtty('389cc05fe58145ceb93407fa56074c23')" title="迷你简粗倩"></a>
            <a  id="ft31" onclick="srtty('8850edf395cb42378f59b5900d69e91b')" title="夏日香气"></a>
            <a  id="ft32" onclick="srtty('160631134d4f42479fed10acd1f0b15a')" title="迷你简菱心"></a>
            <a  id="ft33" onclick="srtty('1d24c920acca4f6f9b1c5cac02faf192')" title="王汉宗拓仿黑体"></a>
            <a  id="ft34" onclick="srtty('b8053974ee104631902526e7b97bfa83')" title="王汉宗粗勘亭流简"></a>
            <a  id="ft35" onclick="srtty('cd3b084f24cd4fbbad2f9e0f0e5d5578')" title="田氏颜体"></a>
            <a  id="ft36" onclick="srtty('b25f2ec876054cebba773f7a865afdd0')" title="梁培生小爨简"></a>
            <a  id="ft37" onclick="srtty('f16b3b886dd74509a28ee8807fbb6c40')" title="雅坊美工14"></a>
            <a  id="ft38" onclick="srtty('25554f05c863478491306618f4ac2789')" title="邯郸-韩鹏毛遂体"></a>
            <a  id="ft39" onclick="srtty('b5eeee7d26744f369a4a39dc23826143')" title="中山行书"></a>
            <a  id="ft40" onclick="srtty('fc09f39d60874c4188b4e1feaca9990a')" title="陈继世-行楷简体"></a>
            <a  id="ft41" onclick="srtty('6214cb0e45284482976020705be16973')" title="晨光大字"></a>
            <a  id="ft42" onclick="srtty('65fb6306d7204c6ba1ae7cf642f3d9ab')" title="叶根友童体简"></a>
            <a  id="ft43" onclick="srtty('1c03ee052a8f478d98ae20470bec26fa')" title="刘雨正毛笔隶书"></a>
            <a  id="ft44" onclick="srtty('1c03ee052a8f478d98ae20470bec26fa')" title="刘雨正毛笔隶"></a>
            <a  id="ft45" onclick="srtty('5b4a49d072c647789bce90b446c99eb8')" title="白舟魂心书体"></a>
        </div>
    </div>
    </div>
















</div>

<div id="jietu">
  	<div id="picbox" style="background:#fff; " >

        <div class="weimoban" id="pic01" style="left:0px; top:0px; width:100%; position: absolute; z-index:0;  " ><img src="{{asset('staticweb/moban/mb-bg-01.jpg')}}"   style=" width: 100%;   " id="ewp1" /></div>
        <div class="drsMoveHandle weimoban selected_type float_div" id="txt1" name="text1" onclick="test(this.id)"  style="position: absolute; z-index: 1001;   font-size:36px; text-align: center; left:100px; width:600px; font-weight:bold; top:168px;"  ondblclick="edit(this.id)"><span contenteditable="true">2号练习本作业反馈</span><div class="del"><img src="{{asset('staticweb/images/del.svg')}}" alt=""></div></div>

        <div class="drsMoveHandle weimoban selected_type float_div" id="txt2" name="text2" data-type-num=2  onclick="test(this.id)"  style="position: absolute; z-index: 1001; color: #222; font-size:26px; text-align: center; left:80px; top:230px; color:red"  ondblclick="edit(this.id)"><span contenteditable="true" >满分作业</span> <div class="del"><img src="{{asset('staticweb/images/del.svg')}}" alt=""></div></div>

        <div class="drsMoveHandle weimoban selected_type float_div" id="txt3" name="text3" data-type-num=3 onclick="test(this.id)"  style="position: absolute; z-index: 1001; color: #222; font-size:26px; text-align: left; left:80px; top:275px; width:640px; border:1px dashed #eee; "  ondblclick="edit(this.id)"><span contenteditable="true">☺</span> <div class="del"><img src="{{asset('staticweb/images/del.svg')}}" alt=""></div></div>
        <div class="drsMoveHandle weimoban selected_type float_div" id="txt4" name="text4" data-type-num=4 onclick="test(this.id)"  style="position: absolute; z-index: 1001; color: #222; font-size:26px; text-align: center; left:80px; top:355px; color:red"  ondblclick="edit(this.id)"><div contenteditable="true" >需要补做（写纸上，单独交）</div><div class="del"><img src="{{asset('staticweb/images/del.svg')}}" alt=""></div></div>
        <div class="drsMoveHandle weimoban selected_type float_div" id="txt5" name="text5" data-type-num=5  onclick="test(this.id)"  style="position: absolute; z-index: 1001; color: #222; font-size:26px; text-align: left; left:80px; top:400px; width:640px; border:1px dashed #eee; "  ondblclick="edit(this.id)"><span contenteditable="true">囧</span> <div class="del"><img src="{{asset('staticweb/images/del.svg')}}" alt=""></div></div>


	</div>
</div>

<div class="card-right">
    <div class="tab">
        <a href="" title="某某班">1</a>
        <a href="" title="某某班">2</a>
        <a href="" title="某某班">3</a>
    </div>
    <div class="title">
        选择学生
    </div>
    <div class="bd userlist">

        <a class="btn-sc class_name">张相昀</a>
        <a class="btn-sc class_name">刘宇哲</a>
        <a class="btn-sc class_name">魏新桐</a>
        <a class="btn-sc class_name">赵紫墨</a>
        <a class="btn-sc class_name">陈井序</a>
        <a class="btn-sc class_name">吕子潇</a>
        <a class="btn-sc class_name">阎毅泽</a>
        <a class="btn-sc class_name">阎羽飞</a>
        <a class="btn-sc class_name">王卓锐</a>
        <a class="btn-sc class_name">余康硕</a>
        <a class="btn-sc class_name">付泽毅</a>
        <a class="btn-sc class_name">刘安琦</a>
        <a class="btn-sc class_name">李博凯</a>
        <a class="btn-sc class_name">杨紫杭</a>
        <a class="btn-sc class_name">张润泽</a>
        <a class="btn-sc class_name">苏以恒</a>
        <a class="btn-sc class_name">王天宇</a>
        <a class="btn-sc class_name">罗文宇</a>
        <a class="btn-sc class_name">黄一鸣</a>
        <a class="btn-sc class_name">姚博航</a>
        <a class="btn-sc class_name">张庭瑜</a>
        <a class="btn-sc class_name">赵旭航</a>
        <hr>
        <div class="k10"></div>
        <a class="btn-sc class_name">亓梦涵</a>
        <a class="btn-sc class_name">侯林菲</a>
        <a class="btn-sc class_name">王晶瑶</a>
        <a class="btn-sc class_name">姜媞媞</a>
        <a class="btn-sc class_name">阎姝含</a>
        <a class="btn-sc class_name">梅淼涵</a>
        <a class="btn-sc class_name">孙灿灿</a>
        <a class="btn-sc class_name">李欣岩</a>
        <a class="btn-sc class_name">米紫萱</a>
        <a class="btn-sc class_name">李思琳</a>
        <a class="btn-sc class_name">罗金熙</a>
        <a class="btn-sc class_name">范思睿</a>
        <a class="btn-sc class_name">王一萱</a>
        <a class="btn-sc class_name">赵怡彤</a>
        <a class="btn-sc class_name">何睿萱</a>
        <a class="btn-sc class_name">牛梓瑶</a>

    </div>
</div>
<div class="c"></div>
</body>
</html>
