<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\SessionImage;
use Vinkla\Hashids\Facades\Hashids;

class SessionImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sessionImages = SessionImage::orderBy('created_at','desc')->get();
        return response()->json([
            'success' => true,
            'payload' => $sessionImages
            ],
            200);
    }

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


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ref_image' => 'string',
            'latest_image' => 'string',
            'sid' => 'required',
            'user_id' => 'required',
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $sesionImage = new SessionImage;
        $sesionImage->user_id = $request->input('user_id');
        $sesionImage->date = $request->input('date');
        $sesionImage->sid = $request->input('sid');
        $sesionImage->organ = $request->input('organ');
        $sesionImage->ref_image = $request->input('ref_image');
        $sesionImage->latest_image = $request->input('latest_image');
        $sesionImage->timestamps = true;
        $sesionImage->save();
        return response()->json([
            'message' => 'Sesion image successfully created',
            'sesionImage' => $sesionImage
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
            'ref_image' => 'string',
            'latest_image' => 'string',
            'sid' => 'required',
            'user_id' => 'required',
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $sesionImage = SesionImage::find($id);
        $sesionImage->user_id = $request->input('user_id');
        $sesionImage->date = $request->input('date');
        $sesionImage->sid = $request->input('sid');
        $sesionImage->organ = $request->input('organ');
        $sesionImage->ref_image = $request->input('ref_image');
        $sesionImage->latest_image = $request->input('latest_image');
        $sesionImage->timestamps = true;
        $sesionImage->save();
        return response()->json([
            'message' => 'Sesion image successfully created',
            'sesionImage' => $sesionImage
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
        $sessionImage = SessionImage::find($id);
        $sessionImage -> delete();
        return response()->json([
            'message' => 'success deleted',    
        ], 200);
    }
}
