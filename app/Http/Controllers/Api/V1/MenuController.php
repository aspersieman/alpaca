<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\MenuItemService as MenuItem;
use App\Services\MenuManagerService as MenuManager;
use App\Models\User;

class MenuController extends Controller
{
  /**
   * MenuController constructor.
   */
  public function __construct()
  {
    $this->middleware('auth:api');
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
          'permission_requirements' => [],
          'label'=>'Dashboard',
          'nav_type' => MenuItem::$NAV_TYPE_NAV,
          'icon'=>'dashboard',
          'route_type'=>'vue',
          'route_name'=>'dashboard',
          'visible'=>true,
          'appBar'=>true,
        ]),
        new MenuItem([
          'group_requirements' => [],
          'permission_requirements' => [],
          'label'=>'Tasks',
          'nav_type' => MenuItem::$NAV_TYPE_NAV,
          'icon'=>'check_circle',
          'route_type'=>'vue',
          'route_name'=>'tasks',
          'visible'=>true,
          'appBar'=>true,
        ]),
        new MenuItem([
          'group_requirements' => [],
          'permission_requirements' => [],
          'label'=>'Notes',
          'nav_type' => MenuItem::$NAV_TYPE_NAV,
          'icon'=>'note',
          'route_type'=>'vue',
          'route_name'=>'notes',
          'visible'=>true,
          'appBar'=>true,
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
          'appBar'=>true,
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
          'appBar'=>false,
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
          'appBar'=>false,
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
          'appBar'=>false,
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
          'appBar'=>false,
        ])
      ]);
      $menus = $menuManager->getFiltered();
    }
    return $this->sendResponseOk($menus,"List resource ok.");
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    return $this->sendResponseCreated([], "Resource created.");
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    return $this->sendResponseOk([], "Resource found.");
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
    return $this->sendResponseUpdated(
      [],
      "Resource updated."
    );
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    return $this->sendResponseDeleted([], "Resource deleted.");
  }
}
