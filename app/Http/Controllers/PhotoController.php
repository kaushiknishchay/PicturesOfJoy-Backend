<?php

namespace App\Http\Controllers;

use App\PhotoCollection;
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

    public function getCollectionPhotos($coll_id)
    {
        $photos = PhotoCollection::find($coll_id);
        return response()->json($photos->photos);
    }
    //
}
