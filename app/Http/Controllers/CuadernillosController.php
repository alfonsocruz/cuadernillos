<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use App\Models\Cuadernillo;

class CuadernillosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $this->authorize('browse', app($dataType->model_name));
        return view('cuadernillos.index');
    }

    public function data(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $this->authorize('browse', app($dataType->model_name));
        
        $length = $request->length;
        $sortBy = $request->column; 
        $orderBy = $request->dir; // ASC | DESC
        $searchValue = $request->search;
        
        $select = "cat_casillas.*, cat_region.Clave, cat_region.Region, cat_df.DistritoFederal, cat_dl.DistritoLocal, cat_municipios_reportes.Municipio";
        $query = Cuadernillo::leftJoin('cat_region', 'cat_region.id', '=', 'cat_casillas.idRegion')
                            ->leftJoin('cat_df', 'cat_df.id', '=', 'cat_casillas.idDF')
                            ->leftJoin('cat_dl', 'cat_dl.id', '=', 'cat_casillas.idDL')
                            ->leftJoin('cat_municipios_reportes', 'cat_municipios_reportes.id', '=', 'cat_casillas.idMunicipioReportes')
                            ->selectRaw($select)
                            ->orderBy($sortBy, $orderBy);
        
        $filters = $request->filters;
        if (isset($filters)) 
        {
            $filters = (array)json_decode($filters);
            if(isset($filters['TieneCuadernillo'])){
                if($filters['TieneCuadernillo'] !== '-1'){
                    $query->where("cat_casillas.TieneCuadernillo", $filters['TieneCuadernillo']);
                }
            }
            if(isset($filters['idRegion'])){
                if($filters['idRegion'] > 0){
                    $query->where("cat_casillas.idRegion", $filters['idRegion']);
                }
            }
            if(isset($filters['idMunicipioReportes'])){
                if($filters['idMunicipioReportes'] > 0){
                    $query->where("cat_casillas.idMunicipioReportes", $filters['idMunicipioReportes']);
                }
            }
            if(isset($filters['Seccion'])){
                if($filters['Seccion'] > 0){
                    $query->where("cat_casillas.Seccion", $filters['Seccion']);
                }
            }
        }
        // eloquentQuery($sortBy, $orderBy, $searchValue);

        $data = $query->paginate($length);
        
        return new DataTableCollectionResource($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->json(['success' => false,'data' => []], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function upload(Request $request){
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $this->authorize('edit', app($dataType->model_name));

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $file->move(public_path('files/tmp'),$fileName);
        return response()->json(['success'=>true, 'data' => $fileName], 200);
    }

    public function storeFile(Request $request){
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $this->authorize('edit', app($dataType->model_name));

        if(!$request->has('id') || ! $request->has('filename')){
            return response()->json(['success'=>false, 'error' => 'No se recibieron los parámetros necesarios'], 200);
        }

        try{
            $tmp_path   = sprintf('%s/%s', public_path('files/tmp'), $request->filename);
            $queryData  = Cuadernillo::find($request->id);
            $municipio  = str_pad($queryData->idMunicipio, 2, "0", STR_PAD_LEFT);
            $seccion    = str_pad($queryData->Seccion, 4, "0", STR_PAD_LEFT);
            $casilla    = trim($queryData->TIPO_CASILLA);
            $ext        = "pdf";
            if(File::exists($tmp_path))
            {
                $ext = File::extension($tmp_path);
            }
            $filename   = sprintf("%s_%s_%s.%s", $municipio, $seccion, $casilla, $ext);
            $dir        = "files/cuadernillos";
            if(!Storage::disk('public')->exists($dir))
            {
                File::makeDirectory(Storage::disk('public')->path($dir), 0777, true, true);
            }

            $path       = sprintf("%s/%s", $dir, $filename);
            $newpath = Storage::disk('public')->path($path);
            File::copy($tmp_path, $newpath);
            File::delete($tmp_path);
            if(Storage::disk('public')->exists($path)){
                $flag = true;
                $queryData->TieneCuadernillo = 1;
                $queryData->NombreArchivo = $filename;
                $queryData->UserUpdate = Auth::id();
                $queryData->save();
            }else{
                $flag = true;
            }
            $response = $flag ? $filename : "Error";
            return response()->json(['success'=>$flag, 'data' => $response], 200);
        }catch(Exception $e){
            return response()->json(['success'=>false, 'error' => $e->getMessage()], 200);
        }
    }
}