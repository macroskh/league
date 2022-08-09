<?php

namespace App\Http\Controllers;

use App\Services\LeagueService;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function index(LeagueService $service, Request $request)
    {
//        $service->grab();
        return response()->json([
            'ids' => $service->all($request->start_timestamp)
        ]);
    }

    /**
     * @param LeagueService $service
     * @param $league_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(LeagueService $service, $league_id)
    {
        if ($name = $service->one($league_id)) {
            return response()->json([
                'name' => $name
            ]);
        }

        return response('Not found',404);
    }
}
