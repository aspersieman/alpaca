<?php
namespace App\Http\Controllers\Front;


use Illuminate\Http\Request;
use App\Components\Core\Menu\MenuItem;
use App\Components\Core\Menu\MenuManager;
use App\User;

class MenuController extends FrontController
{
  /**
   * MenuController constructor.
   */
  public function __construct()
  {
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    /**
     * @var User $currentUser
     */
    $currentUser = \Auth::user();
    $menus = [];
    if ($currentUser) {
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
          'route_name'=>'dashboard',
          'visible'=>true,
        ]),
        new MenuItem([
          'group_requirements' => [],
          'permission_requirements' => ['superuser'],
          'label'=>'Tasks',
          'nav_type' => MenuItem::$NAV_TYPE_NAV,
          'icon'=>'check_circle',
          'route_type'=>'vue',
          'route_name'=>'tasks',
          'visible'=>true,
        ]),
        new MenuItem([
          'group_requirements' => [],
          'permission_requirements' => ['superuser'],
          'label'=>'Reports',
          'nav_type' => MenuItem::$NAV_TYPE_NAV,
          'icon'=>'query_stats',
          'route_type'=>'vue',
          'route_name'=>'reports',
          'visible'=>true,
        ]),
        new MenuItem([
          'label' => 'divider1',
          'nav_type' => MenuItem::$NAV_TYPE_DIVIDER
        ]),
        new MenuItem([
          'group_requirements' => [],
          'permission_requirements' => ['superuser'],
          'label'=>'Admin',
          'nav_type' => MenuItem::$NAV_TYPE_NAV,
          'icon'=>'admin_panel_settings',
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
        ])
      ]);
      $menus = $menuManager->getFiltered();
    }
    return $this->sendResponseOk($menus,"Get menu ok.");
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    return $this->sendResponseOk([],"Created.");
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    return $this->sendResponseOk([]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    return $this->sendResponseUpdated();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    return $this->sendResponseDeleted();
  }
}
