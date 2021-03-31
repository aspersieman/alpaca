<?php
namespace App\Services;


use App\Models\User;

class MenuManagerService
{
    /**
     * the array of MenuItemService objects
     *
     * @var array
     */
    protected $menuItems = [];

    /**
     * the current login user object
     *
     * @var
     */
    protected $user;

    /**
     * set the user
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * add menu
     *
     * @param MenuItemService $menuItem
     */
    public function addMenu(MenuItemService $menuItem)
    {
        $this->menuItems[] = $menuItem;
    }

    /**
     * add menu from given array of MenuItemService objects
     *
     * @param array $menus
     */
    public function addMenus(array $menus)
    {
        foreach ($menus as $menu)
        {
            $this->addMenu($menu);
        }
    }

    /**
     * filter the menu based on given user group & permissions
     *
     * @return \Illuminate\Support\Collection|static
     */
    protected function filter()
    {
        $menus = collect($this->menuItems);

        $menus = $menus->filter(function(MenuItemService $menu)
        {
            if($menu->isDivider()) return true;

            // set all first to true
            $groupRequirementsPassed = true;
            $permissionRequirementsPassed = true;

            // check group requirements
            if($menu->hasGroupRequirements())
            {
                $groupRequirementsPassed = false;

                foreach ($menu->groupRequirements as $groupName)
                {
                    $groupRequirementsPassed = $this->user->inGroup($groupName);

                    if($groupRequirementsPassed) break;
                }
            }

            // check user requirements
            if($menu->hasPermissionRequirements())
            {
                $permissionRequirementsPassed = false;

                $permissionRequirementsPassed = $this->user->hasAnyPermission($menu->permissionRequirements);
            }

            return $groupRequirementsPassed && $permissionRequirementsPassed;
        });

        return $menus;
    }

    /**
     * get all menus
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll()
    {
        return collect($this->menuItems);
    }

    /**
     * get menus that is filtered already
     *
     * @return MenuManager|\Illuminate\Support\Collection
     */
    public function getFiltered()
    {
        return $this->filter();
    }

    /**
     * check if a the menu items has a given menu label
     *
     * @param string $menuLabel
     * @return bool
     */
    public function hasMenu(string $menuLabel)
    {
        $found = false;
        $menus = $this->filter();

        $menus->each(function(MenuItemService $menuItem) use (&$found,$menuLabel)
        {
            if($menuItem->label === $menuLabel) $found = true;
        });

        return $found;
    }
}
