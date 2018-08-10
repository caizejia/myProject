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
                    <p style="font-size: 100px;text-align: center">扫描库位:</p>
                </td>
            </tr>
            <tr>
                <td style="padding: auto">
                    <p style="text-align: center"><input type='text' name="scanCode" style="width: 80%;height: 120px;border: 5px solid red;font-size: 100px" placeholder="库位"/></p>
                </td>
            </tr>
        </table>
        </div>
	</div>

	<script type="text/javascript">
		$("input[name='scanCode']").val("");
		$("input[name='scanCode']").focus();
        $('input:text').bind('keydown', function (e) {
            if (e.which == 13) {
                var scanCodeVal = $('input[name="scanCode"]').val();
                if (scanCodeVal) {
                    location = '/index/returns-lc-number?id='+scanCodeVal;
                } else {
                    $('input[name="scanCode"]').focus();
                }
            }
        });
	</script>
</body>

</html>

