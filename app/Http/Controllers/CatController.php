<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Catalog;

class CatController extends Controller
{
    public function get(Request $request){
        if(!$request->has('catalogs')){
            return response()->json(['success'=>false, 'error' => 'No se recibieron los parámetros necesarios'], 200);
        }

        try{
            $obj = new Catalog;
            $catalogs = $request->catalogs;
            $data = [];
            foreach($catalogs as $cat){
                switch($cat){
                    case 'regiones':
                        $data[$cat] = $obj->getRegions();
                        break;
                    case 'municipios_reportes':
                        $data[$cat] = $obj->getMunicipalities();
                        break;
                    case 'secciones':
                        $data[$cat] = $obj->getSections();
                        break;
                    case 'usuarios':
                        $data[$cat] = $obj->getCapturistUser();
                        break;
                }
            }
            return response()->json(['success' => true, 'data' => $data], 200);
        }catch(Exception $e){
            return response()->json(['success' => false, 'error' => $e->getMessage()], 200);
        }
    }
}
