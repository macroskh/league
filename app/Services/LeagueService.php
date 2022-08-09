<?php

namespace App\Services;

use App\Models\League;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeagueService
{
//Tue, 09 Aug 2022 19:19:18 GMT
    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function grab()
    {
        $url = config('services.league.url');
        $client = new Client();

        try {
            $lastModified = current($client->head($url)->getHeader('Last-Modified'));
        } catch (GuzzleException $exception) {
            // notify
        }

        if (Cache::get('League-Last-Modified') !== $lastModified) {
            Storage::disk('local')->put('actual_json', file_get_contents($url));
            $this->store();
            Cache::put('League-Last-Modified', $lastModified);
        }
    }

    /**
     * @see https://github.com/skolodyazhnyy/json-stream
     * @return void
     */
    protected function store()
    {
        $leagues = json_decode(file_get_contents(config('services.league.url')), true);

        $data = array_map(function ($league) {
            return [
                'external_id' => $league['league_id'],
                'start_timestamp' => $league['start_timestamp'],
                'name' => $league['name'],
            ];
        }, $leagues['infos']);


        League::upsert($data, ['external_id', 'name']);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function all(int $start_timestamp = null)
    {
        return DB::table('leagues')
            ->select('external_id')
            ->when($start_timestamp, function (Builder $query, $start_timestamp) {
                return $query->where('start_timestamp', '>=', $start_timestamp);
            })
            ->pluck('external_id');
    }

    /**
     * @param int $league_id
     * @return mixed|null
     */
    public function one(int $league_id)
    {
        return DB::table('leagues')
            ->where('external_id', $league_id)
            ->value('name');
    }
}
