<?php
/**
 * Created by PhpStorm.
 * User: SHolmes
 * Date: 05-Nov-17
 * Time: 5:04 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array|string name
 * @property array|string description
 * @property string cover
 * @property string colkey
 */
class PhotoCollection extends Model {
    public $timestamps = true;
    public $table = "collection";

    protected $fillable = array('name', 'description', 'cover');
    protected $rules = array('name' => 'required');
    protected $hidden = array('updated_at', 'created_at');
    protected $appends = array('slug', 'albums');

    public function collectionAlbums()
    {
        return $this->hasMany('App\Photos', 'collection_id');
    }

    public function getSlugAttribute()
    {
        return str_slug($this->name);
    }

    public function getAlbumsAttribute()
    {
        return $this->collectionAlbums()->select('name', 'description', 'thumb_url', 'photo_url')->get();
    }

}