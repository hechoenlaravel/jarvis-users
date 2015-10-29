<?php

namespace Modules\Users\Widgets;


use Modules\Users\Entities\User;

/**
 * Class TotalUsersWidget
 * Display Total Users Statistics
 * @package Modules\Users\Widgets
 */
class TotalUsersWidget {

    /**
     * @return string
     */
    public function register()
    {
        $totalUsers = User::count();
        $totalActive = User::where('active', 1)->count();
        $totalInactive = User::where('active', 0)->count();
        return view('users::widgets.total-users', compact('totalUsers', 'totalActive', 'totalInactive'))->render();
    }

}