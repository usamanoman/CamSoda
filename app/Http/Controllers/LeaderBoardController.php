<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\LeaderBoard;
use Validator;

class LeaderBoardController extends Controller
{

    /**
     * Display the leaderboard with respect to the number of given leaders
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id=10)
    {
        //Validate the request params
        $validator = Validator::make(['limit'=>$id], [
            'limit' => 'required|numeric'
        ]);
        //If validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }else{ 
            //Find the leaders and return
            $leaders=LeaderBoard::limit($id)->orderBy('score','desc')->get();
            return $leaders;
        }
    }

    /**
     * Update the leaderboard user with greater score or insert new one.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Validate the request params
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:255',
            'score' => 'required|numeric',
        ]);
        //If validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }else{ 
            //If validation is successful
            $user=LeaderBoard::where(['username'=>$request->input('username')])->first();
            if($user && $user->score<$request->input('score')){ //If score coming in request is greater than previous
                $user->score=$request->input('score');
                $user->save();
                return $user;
            }else if($user && $user->score>=$request->input('score')){
                return response()->json(['username'=>'Username already exist.'],403);
            }else{
                return LeaderBoard::firstOrCreate($request->all());
            }
        }
    }
}
