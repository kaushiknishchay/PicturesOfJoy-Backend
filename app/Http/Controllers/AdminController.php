<?php

namespace App\Http\Controllers;

use App\PhotoCollection;
use App\Photos;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class AdminController extends Controller {

    const UPLOADS = "uploads" . DIRECTORY_SEPARATOR;
    const PUB_URL = DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR;
    const UPLOADS_COVER = self::UPLOADS . "covers" . DIRECTORY_SEPARATOR;
    const UPLOADS_THUMBS = self::UPLOADS_COVER . "thumbs" . DIRECTORY_SEPARATOR;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getCollection($colId = null)
    {
        if ($colId != null && $colId != "") {
            $photoCollection = PhotoCollection::where('colkey', $colId)->first();
            if ($photoCollection != null) {
                return response()->json($photoCollection);
            } else {
                return response()->json(null, 501);
            }
        } else {
            $photoCollection = PhotoCollection::select("id", "name", "colkey")->get();
            return response()->json($photoCollection);
        }
    }

    /**
     * @param $request
     * @return string
     */
    public function createCollection(Request $request)
    {
        $this->validate($request, [
            'collectionCover' => 'required',
            'collectionName' => 'required'
        ]);

        $collectionCover = $request->file('collectionCover');
        $collectionName = $request->input('collectionName');
        $collectionDesc = $request->input('collectionDesc');

        $coverName = "coll_" . str_slug($collectionName) . "_" . uniqid() . ".jpg";
        $thumbFullPath = self::UPLOADS_THUMBS . $coverName;

        $newCollection = new PhotoCollection;
        $newCollection->name = $collectionName;
        $newCollection->description = $collectionDesc;
        $newCollection->cover = $thumbFullPath;
        $newCollection->colkey = str_random(4);

        if ($newCollection->save()) {
            $img = Image::make($collectionCover)
                ->fit(440, 350)
                ->save($thumbFullPath);
            return response()->json(["success" => true], 200);
        } else {
            return response()->json(["success" => false], 200);
        }
    }

    function deleteCollection(Request $request, $colId)
    {

        if ($colId != "" && $colId != null) {
            $collection = PhotoCollection::where('colkey', $colId)->first();
            if ($collection != null) {
                $filepath = base_path("public/" . $collection->cover);
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
                if ($collection->delete()) {
                    return response()->json(["success" => true], 200);
                } else {
                    return response()->json(["success" => false], 200);

                }
            } else {
                return response()->json(["success" => false], 200);
            }
        }
    }

    function updateCollection(Request $request, $colId, $colKey)
    {
        $this->validate($request, [
            'collectionName' => 'required'
        ]);

        $editCol = PhotoCollection::where(['id' => $colId, 'colkey' => $colKey])->first();

        if ($editCol != null) {

            $collectionName = $request->input('collectionName');
            $collectionDesc = $request->input('collectionDesc');

            $editCol->name = $collectionName;
            $editCol->description = $collectionDesc;

            if ($request->hasFile('collectionCover')) {

                $collectionCover = $request->file('collectionCover');
                $coverName = "coll_" . str_slug($collectionName) . "_" . uniqid() . ".jpg";
                $thumbFullPath = self::UPLOADS_THUMBS . $coverName;
                Image::make($collectionCover)
                    ->fit(440, 350)
                    ->save($thumbFullPath);
                $editCol->cover = $collectionCover;

            }

            if ($editCol->save()) {
                return response()->json(["success" => true], 200);
            } else {
                return response()->json(["success" => false], 200);
            }
        } else {
            return response()->json(["success" => false], 200);
        }
    }

    public function getAlbum($albumId = null)
    {
        if ($albumId != null && $albumId != "") {
            $photoAlbum = Photos::where('albumkey', $albumId)->first();
            if ($photoAlbum != null) {
                return response()->json($photoAlbum);
            } else {
                return response()->json(null, 501);
            }
        } else {
            $photoAlbum = Photos::select("id", "name", "albumkey")->get();
            return response()->json($photoAlbum);
        }
    }

    public function createAlbum(Request $request)
    {
        $this->validate($request, [
            'albumName' => 'required',
            'albumCover' => 'required',
            'collectionName' => 'required',
        ]);

        $albumName = $request->input("albumName");
        $albumCover = $request->file("albumCover");
        $albumDesc = $request->input("albumDesc");
        $collectionId = $request->input("collectionName");
        $albumFiles = $request->file("albumFiles");
        $v = array();

        $photosPath = self::UPLOADS . "photos" . DIRECTORY_SEPARATOR;
        $coverName = "album_" . str_slug($albumName) . "_" . uniqid() . ".jpg";
        $thumbFullPath = self::UPLOADS_THUMBS . $coverName;

        if ($request->hasFile('albumFiles')) {
            foreach ($albumFiles as $file) {
                $fName = uniqid() . ".jpg";
                $v = array_prepend($v, $photosPath . $fName);

                $file->move($photosPath, $fName);
            }
        }
        $newAlbum = new Photos;
        $newAlbum->name = $albumName;
        $newAlbum->description = $albumDesc;
        $newAlbum->thumb_url = $thumbFullPath;
        $newAlbum->collection_id = $collectionId;
        $newAlbum->albumkey = str_random(4);
        $newAlbum->photo_url = json_encode($v);

        if ($newAlbum->save()) {
            Image::make($albumCover)->fit(440, 350)
                ->save($thumbFullPath);
            return response()->json(["success" => true], 200);
        } else {
            return response()->json(["success" => false], 200);
        }
    }

    function updateAlbum(Request $request, $albumId, $albumKey)
    {
        $this->validate($request, [
            'albumName' => 'required',
            'collectionName' => 'required',
        ]);

        $photosPath = self::UPLOADS . "photos" . DIRECTORY_SEPARATOR;

        $editAlbum = Photos::where(['id' => $albumId, 'albumkey' => $albumKey])->first();

        if ($editAlbum != null) {

            $albumName = $request->input("albumName");
            $albumDesc = $request->input("albumDesc");
            $collectionId = $request->input("collectionName");
            $photoUpdate = $request->input("albumPhotoUpdate");

            $editAlbum->name = $albumName;
            $editAlbum->description = $albumDesc;
            $editAlbum->collection_id = $collectionId;

            if ($request->hasFile('albumFiles')) {
                $albumFiles = $request->file("albumFiles");
                $v = array();
                foreach ($albumFiles as $file) {
                    $fName = uniqid() . ".jpg";
                    $v = array_prepend($v, $photosPath . $fName);

                    $file->move($photosPath, $fName);
                }
                $editAlbum->photo_url = json_encode($v);
            } else {
                if ($photoUpdate) {
                    $editAlbum->photo_url = $photoUpdate;
                }
            }
            if ($request->hasFile('albumCover')) {

                $albumCover = $request->file("albumCover");

                $coverName = "album_" . str_slug($albumName) . "_" . uniqid() . ".jpg";
                $thumbFullPath = self::UPLOADS_THUMBS . $coverName;
                Image::make($albumCover)->fit(440, 350)
                    ->save($thumbFullPath);
                $editAlbum->thumb_url = $thumbFullPath;
            }

            if ($editAlbum->save()) {
                return response()->json(["success" => true, "aa" => $photoUpdate], 200);
            } else {
                return response()->json(["success" => false], 200);
            }
        } else {
            return response()->json(["success" => false], 200);
        }

    }

    function deleteAlbum(Request $request, $albumKey)
    {

        if ($albumKey != "" && $albumKey != null) {
            $album = Photos::where('albumkey', $albumKey)->first();

            if ($album != null) {
                $filepath = base_path("public/" . $album->thumb_url);
                if (file_exists($filepath)) {
                    unlink($filepath);
                }

                $albumPhotos = json_decode($album->photo_url);
                if ($albumPhotos !== null) {
                    foreach ($albumPhotos as $pho) {
                        $pho = base_path("public" . DIRECTORY_SEPARATOR . $pho);
                        if (file_exists($pho)) {
                            unlink($pho);
                        }
                    }
                }
                if ($album->delete()) {
                    return response()->json(["success" => true], 200);
                } else {
                    return response()->json(["success" => false], 200);

                }
            } else {
                return response()->json(["success" => false], 200);
            }
        }
    }

}
