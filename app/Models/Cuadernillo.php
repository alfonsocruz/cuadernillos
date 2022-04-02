<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class Cuadernillo extends Model
{
    use HasFactory, LaravelVueDatatableTrait;
    
    public $timestamp = false;
    protected $table = 'cat_casillas';
    // protected $dataTableColumns = [
    //     'idDF' => [
    //         'searchable' => false,
    //     ],
    //     'idDL' => [
    //         'searchable' => true,
    //     ],
    //     'idRegion' => [
    //         'searchable' => true,
    //     ],
    //     'idMunicipio' => [
    //         'searchable' => true,
    //     ],
    //     'Seccion' => [
    //         'searchable' => true,
    //     ],
    //     'NombreCasilla' => [
    //         'searchable' => true,
    //     ],
    //     'TieneCuadernillo' => [
    //         'searchable' => true,
    //     ],
    // ];

    // protected $dataTableRelationships = [
    //     "belongsTo" => [
    //         'region' => [
    //             "model" => Region::class,
    //             'foreign_key' => 'idRegion',
    //             'columns' => [
    //                 'Clave' => [
    //                     'searchable' => true,
    //                     'orderable' => true,
    //                 ],
    //             ],
    //         ],
    //     ],
    // ];
    
}
