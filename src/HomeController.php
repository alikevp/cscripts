<?php

namespace Callscripts;

use Illuminate\Http\Request;
use Callscripts\CallLog;
use Callscripts\Questions;
use Callscripts\Topics;

class HomeController extends Controller
{

	public function getQuestion(Request $request)
	{
		/*
			Здесь же происходит сохранение ответа на прошлый вопрос
		*/
		$getNextQuestion = Questions::where('topic', '=', $request->input('topic'));
		if($request->has('next_id')){
			$getNextQuestion = $getNextQuestion->where('id', $request->input('next_id'));
			# Сохранение ответа на поставленный вопрос
			CallLog::where('call_id', $request->call_id)
				->where('question_id', $request->parent_id)
				->update(
					[
						'variant_id'	=> $request->answered,
						'updated_at'	=> date("Y-m-d H:i:s", time())
					]
				);
		}else{
			$getNextQuestion = $getNextQuestion->where('parent_id', '=', $request->input('parent_id'));
		}
		$returnQuestion = [];
		if($getNextQuestion->count()>0){
			$returnQuestion = [
				'has_response' 	=> true,
				'id'			=> $getNextQuestion->first()->id,
				'question_text'	=> $getNextQuestion->first()->question_text,
				'variants'		=> json_decode($getNextQuestion->first()->variants),
				'request'		=> $request->toArray()
			];
			# Запись вопроса в рамках текущего диалога
			CallLog::insert(
				[
					'call_id'		=> $request->call_id,
					'question_id'	=> $getNextQuestion->first()->id,
					'variant_id'	=> 0,
					'created_at'	=> date("Y-m-d H:i:s", time())
				]
			);
		}else{
			$returnQuestion = [
				'has_response'	=> false,
				'request'		=> $request->toArray()
			];
		}
		return response()->json($returnQuestion);
	}

    public function begin()
    {
    	$getCallTopics = Topics::get();
    	return view('home')
    		->with('topics', $getCallTopics);
    }

}
