<?php

namespace App\Http\Controllers;

use App\PhotoCollection;
use App\Photos;
use Illuminate\Http\Request;

class PhotoController extends Controller {
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        print_r($request->request->get("username"));
        return "ghhjkj";
    }

    public function getCollectionsList($colKey = null)
    {
        if ($colKey != null && $colKey != "" && strlen($colKey) == 4) {
            $photoCollection = PhotoCollection::where('colkey', $colKey)
                ->first();
            $par = $photoCollection->toArray();
            unset($par['id']);
            unset($par['cover']);
            unset($par['colkey']);
            unset($par['slug']);

//            $photoCollection->albums = $photoCollection->collectionAlbums()->select('name', 'id')->get();
            return response()->json($par);
        } else {
            $photoCollection = PhotoCollection::select('name', 'description', 'colkey', 'cover')->get();
            return response()->json($photoCollection);
        }
    }

    public function getRandomWorks($count)
    {
        $allAlbums = Photos::select('photo_url')->get()->toArray();
        $photos = array();
        foreach ($allAlbums as $album) {
            unset($album['slug']);
            $photos = array_merge_recursive($photos, json_decode($album['photo_url']));
        }
        shuffle($photos);
        return response()->json(array_slice($photos, 0, $count));
    }

    public function getAlbumPhotos($albumKey)
    {
        if ($albumKey != null && $albumKey != "" && strlen($albumKey) >= 3) {

            $photos = Photos::where('albumkey', $albumKey)->select('name', 'description', 'thumb_url', 'photo_url')->first();
            return response()->json($photos);
        } else {
            return response()->json(["success" => false], 500);
        }
    }
}
