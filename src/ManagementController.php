<?php

namespace Callscripts;

use Illuminate\Http\Request;
use Callscripts\Topics;
use Callscripts\Questions;

class ManagementController extends Controller
{
    public function default()
    {
    	$getQuestions = Topics::getAssociated();
    	return view('management')->with('questionsList', $getQuestions);
    }

    public function updateQuestion(Request $request)
    {
        $variants = [];
        foreach($request->variant_ids as $currentVarIdx=>$currentID){
            $variants[] = [
                'id'    => $currentID,
                'link'  => $request->input('variant_links.'.$currentVarIdx),
                'title' => $request->input('variant_titles.'.$currentVarIdx)
            ];
        }
        Questions::where('id', $request->id)
            ->update(
                [
                    'question_text' => $request->question_text,
                    'variants'      => json_encode($variants),
                    'updated_at'    => date("Y-m-d H:i:s", time())
                ]
            );
        return response()->json($request->toArray());
    }

    public function createQuestion(Request $request)
    {
        $variants = [];
        foreach($request->variant_ids as $currentVarIdx=>$currentID){
            $variants[] = [
                'id'    => $currentID,
                'link'  => $request->input('variant_links.'.$currentVarIdx),
                'title' => $request->input('variant_titles.'.$currentVarIdx)
            ];
        }
        Questions::insert(
            [
                'question_text' => $request->question_text,
                'variants'      => json_encode($variants),
                'topic'         => $request->topic,
                'parent_id'     => $request->parent_id,
                'type'          => 2,
                'created_at'    => date("Y-m-d H:i:s", time())
            ]
        );
        return response()->json($request->toArray());
    }

    public function getQuestionData(Request $request)
    {
    	$question = Questions::where('id', '=', $request->input('question_id'))->get();
    	if($question->count()>0){
            $questionAssoc = Questions::where('topic', $question->first()->topic)->get();
    		$return = $question->first()->toArray();
            foreach($questionAssoc as $currentQuestion){
                $return['linked'][$currentQuestion->id] = $currentQuestion->question_text;
            }
    	}else{
    		$return = [];
    	}
    	return response()->json($return);
    }
}
