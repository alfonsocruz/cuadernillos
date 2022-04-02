<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Catalog extends Model
{
    use HasFactory;

    public function getRegions(){
        try{
            $data = DB::table('cat_region')->select('id AS value', 'Region AS text')->where('id', '>', 1)->get();
            $response = collect([['value' => 0, 'text' => 'Todas']]);
            $response = $response->merge($data);
            return $response;
        }catch(Exception $e){
            return [];
        }
    }

    public function getMunicipalities(){
        try{
            $data = DB::table('cat_municipios_reportes')->select('id AS value', 'Municipio AS text', 'idRegion')->where('id', '!=', 61)->get();
            $response = collect([['value' => 0, 'text' => 'Todos']]);
            $response = $response->merge($data);
            return $response;
        }catch(Exception $e){
            return [];
        }
    }

    public function getSections(){
        try{
            $data = DB::table('secciones')->select('Seccion AS value', 'Seccion AS text', 'idRegion', 'idDF', 'idDL', 'idMunicipio', 'idMunicipioReportes')->whereIn('Seccion', function($query){
                $query->select('Seccion')->from('cat_casillas');
            })->get();
            $response = collect([['value' => 0, 'text' => 'Todas']]);
            $response = $response->merge($data);
            return $response;
        }catch(Exception $e){
            return [];
        }
    }
}
