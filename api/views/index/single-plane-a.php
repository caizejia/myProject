<?php
/**
 * Created by PhpStorm.
 * User: 秦洋
 * Date: 2018/3/8
 * Time: 17:20
 */

$product = Yii::$app->db->createCommand("select * from products WHERE id = $pdf[website]")->queryOne();
$order = new \app\models\Orders();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $pdf['id'];?></title>
</head>





<body><?php $src = '../pdf/'. $pdf['lc_number'] . '.png';?>
<img src="<?php echo $src;?>" alt="">
</body>

</html>





