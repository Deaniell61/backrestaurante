<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\ComidaMenu;
use App\Comidas;
use Response;
use Validator;
use Storage;

class ComidasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Comidas::all(), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'       => 'required',
        ]);
        if ( $validator->fails() ) {
            $returnData = array (
                'status' => 400,
                'message' => 'Invalid Parameters',
                'validator' => $validator
            );
            return Response::json($returnData, 400);
        }
        else {
            try {
                $newObject = new Comidas();
                $newObject->nombre            = $request->get('nombre');
                $newObject->codigo            = $request->get('codigo');
                $newObject->costo             = $request->get('costo');
                $newObject->save();
                return Response::json($newObject, 200);
            
            } catch (Exception $e) {
                $returnData = array (
                    'status' => 500,
                    'message' => $e->getMessage()
                );
                return Response::json($returnData, 500);
            }
        }
    }
    public function uploadAvatar(Request $request, $id) {
        $objectUpdate = Comidas::find($id);
        if ($objectUpdate) {

            $validator = Validator::make($request->all(), [
                'avatar'      => 'required|image|mimes:jpeg,png,jpg'
            ]);

            if ($validator->fails()) {
                $returnData = array(
                    'status' => 400,
                    'message' => 'Invalid Parameters',
                    'validator' => $validator->messages()->toJson()
                );
                return Response::json($returnData, 400);
            }
            else {
                try {
                    $path = Storage::disk('s3')->put('comidas', $request->avatar);

                    $objectUpdate->foto = Storage::disk('s3')->url($path);
                    $objectUpdate->save();

                    return Response::json($objectUpdate, 200);
                }
                catch (Exception $e) {
                    $returnData = array(
                        'status' => 500,
                        'message' => $e->getMessage()
                    );
                }

            }

            return Response::json($objectUpdate, 200);
        }
        else {
            $returnData = array(
                'status' => 404,
                'message' => 'No record found'
            );
            return Response::json($returnData, 404);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $objectSee = Comidas::find($id);
        if ($objectSee) {
            return Response::json($objectSee, 200);
        
        }
        else {
            $returnData = array (
                'status' => 404,
                'message' => 'No record found'
            );
            return Response::json($returnData, 404);
        }
    }

    public function comidasByMenu($id)
    {
        $objectSee1 = Comidas::all();
        $objectSee = ComidaMenu::whereRaw('menu=?',$id)->get();

        $comidas = [];

        if ($objectSee1) {
            foreach ($objectSee1 as $value1) {
                $value1->agregado = 0;
                $value1->addId = null;
                foreach ($objectSee as $value) {
                    if($value1->id == $value->comida)
                    {
                        $value1->agregado = 1;
                        $value1->addId = $value->id;
                    }
                }
                array_push($comidas, $value1);
            }
            return Response::json($comidas, 200);
        
        }
        else {
            $returnData = array (
                'status' => 404,
                'message' => 'No record found'
            );
            return Response::json($returnData, 404);
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        $objectUpdate = Comidas::find($id);
        if ($objectUpdate) {
            try {
                $objectUpdate->nombre            = $request->get('nombre', $objectUpdate->nombre);
                $objectUpdate->codigo            = $request->get('codigo', $objectUpdate->codigo);
                $objectUpdate->costo             = $request->get('costo', $objectUpdate->costo);
                $objectUpdate->estado            = $request->get('estado', $objectUpdate->estado);
                
                $objectUpdate->save();
                return Response::json($objectUpdate, 200);
            } catch (Exception $e) {
                $returnData = array (
                    'status' => 500,
                    'message' => $e->getMessage()
                );
                return Response::json($returnData, 500);
            }
        }
        else {
            $returnData = array (
                'status' => 404,
                'message' => 'No record found'
            );
            return Response::json($returnData, 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $objectDelete = Comidas::find($id);
        if ($objectDelete) {
            try {
                Comidas::destroy($id);
                return Response::json($objectDelete, 200);
            } catch (Exception $e) {
                $returnData = array (
                    'status' => 500,
                    'message' => $e->getMessage()
                );
                return Response::json($returnData, 500);
            }
        }
        else {
            $returnData = array (
                'status' => 404,
                'message' => 'No record found'
            );
            return Response::json($returnData, 404);
        }
    }
}
