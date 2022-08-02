<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Scan;

class ScanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $scans = Scan::orderBy('created_at','desc')->get();
        return response()->json([
            'success' => true,
            'payload' => $scans
            ],
            200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getByUserId(Request $request, $id): JsonResponse
    {
        try {
            $sessionImages = SessionImage::where('user_id', Hashids::decode($id)[0])->orderBy('created_at','desc')->get();
            return response()->json([
                'success' => true,
                'payload' => $sessionImages
                ],
                200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'payload' => $e->getMessge()
                ],
                500);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fileName' => 'required|string',
            'folderName' => 'required|string',
            'user_id' => 'required',
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $scan = new Scan;
        $scan->user_id = $request->input('user_id');
        $scan->fileName = $request->input('fileName');
        $scan->folderName = $request->input('folderName');
        $scan->timestamps = true;
        $scan->save();
        return response()->json([
            'message' => 'Scan successfully created',
            'scan' => $scan
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
            'fileName' => 'required|string',
            'folderName' => 'required|string',
            'user_id' => 'required',
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $scan = Scan::find($id);
        $scan->user_id = $request->input('user_id');
        $scan->fileName = $request->input('fileName');
        $scan->folderName = $request->input('folderName');
        $scan->timestamps = true;
        $scan->save();
        return response()->json([
            'message' => 'Scan successfully updated',
            'scan' => $scan
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $scan = Scan::find($id);
        $scan -> delete();
        return response()->json([
            'message' => 'success deleted',    
        ], 200);
    }
}
