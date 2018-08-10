<?php
use yii\helpers\Url;
$this->title = '库位';
?>

<script src="../js/jquery.min.js"></script>
<script src="../js/plugins/layer/layer.min.js"></script>

<!-- 第三方插件 -->
<script src="../js/plugins/pace/pace.min.js"></script>
<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<link rel="shortcut icon" href="../favicon.ico">
<link href="../css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
<link href="../css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
<link href="../css/animate.css" rel="stylesheet">
<link href="../css/style.css?v=4.1.0" rel="stylesheet">
<html>

<head>
	<title>库位</title>
	<meta charset="utf-8">
</head>

<body>
	<div style="width: 100%">
        <table style="width: 100%">
            <tr>
                <td width="100%" style="padding: auto">
                    <p style="font-size: 100px;text-align: center">扫描物流单号:</p>
                </td>
            </tr>
            <tr>
                <td style="padding: auto">
                    <p style="text-align: center;font-size: 100px;color: green">库位：<?php echo $id;?>&nbsp;&nbsp;<button class="btn btn-primary" type="button" onclick="change_kw()" style="font-size: 70px">更换</button><input type='hidden' name="scanCode"  value="<?php echo $id;?>" readonly="readonly"/></p>
                </td>
            </tr>
            <tr>
                <td style="padding: auto">
                    <p style="text-align: center"><input type='text' name="lc" style="width: 80%;height: 120px;border: 5px solid red;font-size: 100px;" placeholder="请扫描物流单号"/></p>
                </td>
            </tr>
            <tr>
                <td style="padding: auto">
                    <p style="text-align: center"><input type='text' name="message" readonly="readonly" id="message" style="width: 100%;height: 120px;border: none;font-size: 100px;color: red;text-align: center"/></p>
                </td>
            </tr>
            <tr>
                <td style="padding: auto">
                    <p style="text-align: center"><button class="btn btn-success" onclick="submit_data()" style="font-size: 60px">上传数据</button></p>
                </td>
            </tr>
            <tr>
                <td style="padding: auto">
                    <p style="text-align: center;font-size: 60px">以下为订单数据</p>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 50px">
                    <p style="text-align: center"><textarea name="lc_number" id="lc_number" cols="20" rows="100" readonly="readonly" style="border: 5px solid black;font-size: 80px"></textarea></p>
                </td>
            </tr>
        </table>
        </div>
	</div>

	<script type="text/javascript">
        $("input[name='lc']").val("");
        $("input[name='lc']").focus();
        $('input:text').bind('keydown', function (e) {
            if (e.which == 13) {
                $("input[name='message']").val("");
                var lc = $('input[name="lc"]').val();
                if (lc) {
                    $.post("<?=Url::to(['/index/returns-lc-check'])?>", {'id':"<?php echo $id;?>",'_csrf':"<?= Yii::$app->request->csrfToken ?>",'lc':lc}, function (msg) {
                        var data = JSON.parse(msg);
                        console.log(data);
                        if(data.res == true){
                            if($("#lc_number").val()){
                                lc = $("#lc_number").val() + "\r\n" + lc;
                            }
                            $("#lc_number").val(lc);
                            $("input[name='lc']").val("");
                            $("input[name='lc']").focus();
                        }else{
                            $("#message").val(data.msg);
                            $("input[name='lc']").val("");
                            $("input[name='lc']").focus();
                        }
                    })
                } else {
                    $('input[name="lc"]').focus();
                }
            }
        });

        function change_kw() {
            location = '/index/returns-kw';
        }
        
        function submit_data() {
            var lc_number = $("#lc_number").val();
            if(lc_number){
                $.post("<?=Url::to(['/index/returns-lc-submit'])?>", {'id':"<?php echo $id;?>",'_csrf':"<?= Yii::$app->request->csrfToken ?>",'lc_number':lc_number}, function (msg) {
                    var data = JSON.parse(msg);
                    console.log(data);
                    if(data.res == true){
                        $("#lc_number").val('');
                        $("input[name='lc']").val("");
                        $("input[name='lc']").focus();
                        $("#message").val('成功');
                    }else{
                        $("#message").val(data.msg);
                        $("input[name='lc']").val("");
                        $("input[name='lc']").focus();
                    }
                })
            }
        }
	</script>
</body>

</html>

