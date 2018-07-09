<?php

use App\Image;

/*
Route::get('/trigger', 'ImageController@index');

Route::get('/', function () {

    $board = ['url' => 'https://ar.pinterest.com/nicobeta/fox.rss/'];
    $str = file_get_contents($board['url']);

    $data = new SimpleXMLElement($str);
    $board['name'] = (string) $data->channel->title;
    $items = [];
    foreach ($data->channel->item as $item) {
        $items[] = (array) $item;
    }

    $re = '/(http(s?):)([\/|.|\w|\S|-])*\.(?:jpg|gif|png)/m';

    $result = collect($items)->map(function ($item) use ($re) {
        preg_match_all($re, $item['description'], $matches, PREG_SET_ORDER, 0);
        if (count($matches)) {
            $url = str_replace('236x/', 'originals/', $matches[0]);
            $item['url'] = $url[0];
        } else {
            $item['url'] = 'NOT_FOUND';
        }
        return $item;
    })->filter(function ($item) {
        return !Image::where('url', $item['url'])->first();
    })->map(function ($item) use ($board) {
        $name = str_slug($item['url']);
        if (array_key_exists('title', $item) && is_string($item['title'])) {
            $name = $item['title'];
        }

        $image = Image::create([
            'name' => $name,
            'url' => $item['url'],
            'pub_date' => $item['pubDate'],
            'board_name' => $board['name'],
            'board_url' => $board['url']
        ]);
        return $image->toArray();
    });

    return $result;
});
*/

Route::get('/store', function () {
    $image = Image::where('imported', '=', 0)->first();

    $pathinfo = pathinfo($image['url']);
    $contents = file_get_contents($image['url']);

    $path = '/D&D/Gallery/' . $image['board_name'] . '/' . $pathinfo['basename'];
    $result = Storage::disk('dropbox')->put($path, $contents);

    if ($result) {
        $image->imported = 1;
        $image->save();
    }
    return $image;
});
