<?php

namespace Callscripts;

use Illuminate\Database\Eloquent\Model;
use Callscripts\Questions;

class Topics extends Model
{
    protected $table = 'topics';

    public static function getAssociated($topic=-1)
    {
    	if($topic == -1){
    		$getTopics = self::get();
    	}else{
    		$getTopics = self::where('id', '=', $topic)->get();
    	}
    	if($getTopics->count()>0){
    		$topicsData = $getTopics->toArray();
    		foreach($topicsData as $thisTopicIdx=>$currentTopic){
	    		$currentTopicQuestions = Questions::where('topic', '=', $currentTopic['id'])->get();
	    		if($currentTopicQuestions->count()>0){
	    			$topicsData[$thisTopicIdx]['questions'] = $currentTopicQuestions->toArray();
	    		}else{
	    			$topicsData[$thisTopicIdx]['questions'] = [];
	    		}
	    	}
    		return $topicsData;
    	}else{
    		return [];
    	}
    }
}
