<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $links = Link::where('user_id', $user->id)->get();

        return $links->map(function($link){
            $orders =  Order::where('code', $link->code)->where('complete', 1)->get();

            return [
                'code' => $link->code,
                'count' => $orders->count(),
                'revenue' => $orders->sum('ambassador_revenue'),
            ];
        });

    }

    public function rankings(){

        dd('ok');
        // return Redis::zrange('rankings', 0 , -1, 'WITHSCORES');
        $ambassadors = User::ambassador()->get();

        $rankings = $ambassadors->map(fn($ambassador) => [
            'name' => $ambassador->name,
            'revenue' => $ambassador->revenue
        ]);
        return $rankings->sortByDesc('revenue')->values();

    }
}
