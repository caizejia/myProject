<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
                    // ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                    ['label' => '添加用户', 'icon' => 'plus', 'url' => ['/admin/user/signup']],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => '权限管理',
                        'icon' => 'sitemap',
                        'url' => '#',
                        'items' => [
                            ['label' => '分配权限', 'icon' => 'legal', 'url' => ['/admin/assignment'],],
                            ['label' => '权限', 'icon' => 'key', 'url' => ['/admin/permission'],],
                            ['label' => '角色', 'icon' => 'group', 'url' => ['/admin/role'],],
                            ['label' => '用户', 'icon' => 'user', 'url' => ['/admin/user'],],
                            ['label' => '路由', 'icon' => 'exchange', 'url' => ['/admin/route'],],
                            // ['label' => '菜单', 'icon' => 'tasks', 'url' => ['/admin/menu'],],
                            // ['label' => '规则', 'icon' => 'plus', 'url' => ['/admin/rule'],],
                        ],
                    ],
                    [
                        'label' => '部门管理',
                        'icon' => 'sitemap',
                        'url' => '#',
                        'items' => [
                            ['label' => '部门列表', 'icon' => 'legal', 'url' => ['/admin/user/dept'],],
                        ],
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>
