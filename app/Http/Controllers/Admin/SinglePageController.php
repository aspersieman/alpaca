<?php
namespace App\Http\Controllers\Admin;


use App\Components\Core\Menu\MenuItem;
use App\Components\Core\Menu\MenuManager;
use App\Components\User\Models\User;

class SinglePageController extends AdminController
{
    public function displaySPA()
    {
        /**
         * @var User $currentUser
         */
        $currentUser = \Auth::user();
        $menuManager = new MenuManager();
        $menuManager->setUser($currentUser);
        $menuManager->addMenus([
            new MenuItem([
                'group_requirements' => [],
                'permission_requirements' => ['superuser'],
                'label'=>'Dashboard',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon'=>'dashboard',
                'route_type'=>'vue',
                'route_name'=>'admin.dashboard',
                'visible'=>true,
            ]),
            new MenuItem([
                'group_requirements' => [],
                'permission_requirements' => ['superuser'],
                'label'=>'User',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon'=>'person',
                'route_type'=>'vue',
                'route_name'=>'admin.users.list',
                'visible'=>true,
            ]),
            new MenuItem([
                'group_requirements' => [],
                'permission_requirements' => ['superuser'],
                'label'=>'Files',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon'=>'cloud_circle',
                'route_type'=>'vue',
                'route_name'=>'admin.files',
                'visible'=>true,
            ]),
            new MenuItem([
                'group_requirements' => [],
                'permission_requirements' => ['superuser'],
                'label'=>'Settings',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon'=>'settings',
                'route_type'=>'vue',
                'route_name'=>'admin.settings',
                'visible'=>true,
            ]),
            new MenuItem([
                'nav_type' => MenuItem::$NAV_TYPE_DIVIDER
            ])
        ]);

        $menus = $menuManager->getFiltered();

        view()->share('nav',$menus);

        return view('welcome');
    }
}
