<?php
/**
 * 自定义方法类.
 */

namespace api\components;

use Yii;

class Funs
{
    /**
     * 获取IP
     */
    public static function get_ip()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('REMOTE_ADDR')) {
            $onlineip = getenv('REMOTE_ADDR');
        } else {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }

        return $onlineip;
    }

    /**
     * 上传文件流到fdfs服务器
     * @param @fileList 上传文件列表 
     * @param @postParam 上传参数
     *
     * @author YXH
     * @date 2018/06/23
     *
     * @return mixed 
     */
    public static function uploadFile($fileList, $format = 'jpg')
    {
        $params = Yii::$app->params['fdfs'];
        $count = count($fileList);
        if ($count < 1) {
            // 无文件
            return false;
        }

        if ($count == 1) {
            // 单文件
            $file = new \CURLFile(realpath($fileList[0]));
            $data = [$params['fileField'] => $file];
        } else {
            $file = $data = [];
            for ($i = 0; $i < $count; ++$i) {
                // curl 多文件上传
                $file = new \CURLFile(realpath($fileList[$i]));
                $data[$params['fileField'].'['.$i.']'] = $file;
            }
        }

        // 13位时间戳
        list($s1, $s2) = explode(' ', microtime());  
        $timestamp = (float) sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
        // 加密token
        $token = md5($params['salt'].$timestamp);
        $postParam = [
            'time' => $timestamp,
            'token' => $token,
            'file_field' => $params['fileField'],
            'format' => $format,
        ];
        $param = array_merge($postParam, $data);
        $ch = curl_init($params['baseUrl']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 500);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) {
            return curl_error($ch);
        }

        curl_close($ch);
        if (!$response) {
            return false;
        }

        return $response;
    }

    /**
     * 数组不重复排列集合
     *
     * @addby YXH
     * @date 2018/06/25
     */
    public static function get_arr_set($arrs, $currentIndex = -1)
    {
        //总数组
        static $totalArr;
        //总数组下标计数
        static $totalArrIndex;
        //输入的数组长度
        static $totalCount;
        //临时拼凑数组
        static $tempArr;

        //进入输入数组的第一层，清空静态数组，并初始化输入数组长度
        if ($currentIndex < 0) {
            $totalArr = [];
            $totalArrIndex = 0;
            $tempArr = [];
            $totalCount = count($arrs) - 1;
            self::get_arr_set($arrs, 0);
        } else {
            //循环第$currentIndex层数组
            foreach ($arrs[$currentIndex]['value'] as $v) {
                //如果当前的循环的数组少于输入数组长度
                if ($currentIndex < $totalCount) {
                    //将当前数组循环出的值放入临时数组
                    $tempArr[$currentIndex]['value'] = $v;
                    $tempArr[$currentIndex]['attr'] = $arrs[$currentIndex]['attr'];
                    //继续循环下一个数组
                    self::get_arr_set($arrs, $currentIndex + 1);
                }
                //如果当前的循环的数组等于输入数组长度(这个数组就是最后的数组)
                elseif ($currentIndex == $totalCount) {
                    //将当前数组循环出的值放入临时数组
                    $tempArr[$currentIndex]['value'] = $v;
                    $tempArr[$currentIndex]['attr'] = $arrs[$currentIndex]['attr'];
                    //将临时数组加入总数组
                    $totalArr[$totalArrIndex] = $tempArr;
                    //总数组下标计数+1
                    ++$totalArrIndex;
                }
            }
        }

        return $totalArr;
    }

    /**
 * 获取分页信息
 * @param model 模型对象
 * @param where where条件
 * @param currentpage 当前页
 * @param pageSIze 分页数
 *
 * @author YXH
 * @date 2018/07/03
 *
 * @return mixed
 */
    public static function get_page_info($model, $where = [], $currentPage = 1, $pageSize = 20)
    {
        $pagination = [];
        $pagination['totalCount'] = $model->find()->where($where)->andWhere(['is_del' => 0])->count();
        $pagination['currentPage'] = $currentPage + 1;
        $pagination['pageCount'] = ceil($pagination['totalCount'] / $pageSize);
        $pagination['perPage'] = $pageSize;

        return $pagination;
    }
}
