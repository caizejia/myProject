<?php
return [
    /* ----- START 百度图片搜索配置参数 ----- */
    'baiduAppId' => '11221265',
    'baiduAppKey' => 'xDMDjGBZ89t6j8TuEfbe8cul',
    'baiduSecretKey' => 'qLcd62gxghgMGie9u8hZbtiGNLaHK0Vb',
    'minScore' => 0.6,
    'maxScore' => 0.8,
    // ------ END ------

    /* ---------- START FDFS文件上传配置参数 -------- */
    'fdfs' => [
        'baseUrl' => 'http://img.orkotech.com/fdfs_upload_test.php',
        'salt' => 'salt001',
        'fileField' => 'fdfs_file',
    ],
    // ------- END -------
    'adminEmail' => 'admin@example.com',

    // 登录过期时间
    'uid_expire_time' => time() + (3600 * 24),
];
