<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    const STATUS_ACTIVE = '1';
    protected $fillable = ['keyword', 'status', 'category_id', 'volume', 'creator_user_id'];
    
    public function getResults()
    {
        return $this->hasMany('App\Models\Result')->get();
    }
    
    public function getLastCheckResults($agent_id = 1)
    {
        if ($check = $this->hasOne('App\Models\Check')->where('agent_id', $agent_id)->orderBy('checks.id', 'DESC')->first()) {
			return $check->getResults();
		}

        return [];
    }
    
    public function getLastTextCheckResults($agent_id = 1)
    {
        if ($check = $this->hasOne('App\Models\Check')->where('agent_id', $agent_id)->orderBy('checks.id', 'DESC')->first()) {
            return $check->getTextResults();
        }

        return [];
    }
    
    public function getLastPlaCheckResults($agent_id = 1)
    {
        if ($check = $this->hasOne('App\Models\Check')->where('agent_id', $agent_id)->orderBy('checks.id', 'DESC')->first()) {
            return Result::where('keyword_id', $this->id)->where('class_id', Result\Pla::CLASS_ID)->where('check_id', $check->id)->get();
        }

        return [];
    }
    
    public function getLastResult()
    {
        return $this->hasOne('App\Models\Result')->orderBy('results.id', 'DESC')->get();
    }
}
