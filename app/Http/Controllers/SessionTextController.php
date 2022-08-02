<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\SessionText;

class SessionTextController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sessionTexts = SessionText::orderBy('created_at','desc')->get();
        return response()->json([
            'success' => true,
            'payload' => $sessionTexts
            ],
            200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'sid' => 'required',
            'user_id' => 'required',
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $sessionText = new SessionText;
        $sessionText->user_id = $request->input('user_id');
        $sessionText->sid = $request->input('sid');
        $sessionText->date = $request->input('date');
        $sessionText->textFile = $request->input('textFile');
        $sessionText->timestamps = true;
        $sessionText->save();
        return response()->json([
            'message' => 'Session text successfully created',
            'sessionText' => $sessionText
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'sid' => 'required',
            'user_id' => 'required',
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $sessionText = SessionText::find($id);
        $sessionText->user_id = $request->input('user_id');
        $sessionText->sid = $request->input('sid');
        $sessionText->date = $request->input('date');
        $sessionText->textFile = $request->input('textFile');
        $sessionText->timestamps = true;
        $sessionText->save();
        return response()->json([
            'message' => 'Session text successfully updated',
            'sessionText' => $sessionText
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sessionText = SessionText::find($id);
        $sessionText -> delete();
        return response()->json([
            'message' => 'success deleted',    
        ], 200);
    }
}
