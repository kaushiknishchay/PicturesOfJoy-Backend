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
 * @property string thumb_url
 * @property array|string collection_id
 * @property string photo_url
 * @property string albumkey
 */
class Photos extends Model {
    public $timestamps = true;
    public $table = "photos";
    protected $appends = array('slug');

    public function getSlugAttribute()
    {
        return str_slug($this->name);
    }

    public function collection()
    {
        return $this->belongsTo('App\PhotoCollection', 'collection_id', 'id');
    }
}