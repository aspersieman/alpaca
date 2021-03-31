<?php
namespace App\Utilities;

use App\Utilities\Menu\MenuManager;
use App\Models\User;

class MenuHelper
{
  public static function initMenu()
  {
    /**
     * @var User $currentUser
     */
    $currentUser = \Auth::user();
    $menus = config('menu',[]);
    $menuManager = new MenuManager();
    $menuManager->setUser($currentUser);
    $menuManager->addMenus($menus);

    $menus = $menuManager->getFiltered();

    view()->share('nav',$menus);
  }
}
