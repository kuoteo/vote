<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Prettus\Repository\Contracts\Transformable;

use Prettus\Repository\Traits\TransformableTrait;

use App\Traits\ReturnFormatTrait;

use App\Traits\TrimTrait;

use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Auth\Authenticatable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

/**
 * Class Behalf.
 *
 * @package namespace App\Models;
 */
class Behalf extends Model implements Transformable, JWTSubject, AuthenticatableContract
{
    use TransformableTrait;

    use Notifiable;

    use ReturnFormatTrait;

    use Authenticatable;

    use TrimTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name', 'student_id', 'is_sign', 'is_vote', 'vote_model_id','id'
	];
	
    protected $table = 'behalf';

    public $timestamps = false;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 禁止自动添加更新时间
     * @author leekachung <leekachung17@gmail.com>
     * @return [type] [description]
     */
    public function getUpdatedAtColumn()
    {
        return null;
    }




    const is_sign_yes= 1;
    const is_sign_no=0;

    public function sign($ind=null){
        $arr=[
          self::is_sign_yes=>"已签到",
          self::is_sign_no=>"未签到",
        ];
        if($ind!==NULL){
            return array_key_exists($ind,$arr)?$arr[$ind]:$arr[$ind];

        }
        return $arr;
    }

    const is_vote_yes= 1;
    const is_vote_no=0;

    public function vote($ind=null){
        $arr=[
            self::is_sign_yes=>"已投票",
            self::is_sign_no=>"未投票",
        ];
        if($ind!==NULL){
            return array_key_exists($ind,$arr)?$arr[$ind]:$arr[$ind];

        }
        return $arr;
    }
}
