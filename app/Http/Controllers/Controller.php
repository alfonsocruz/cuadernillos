<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $code;

    public function __construct(){        
        // $this->setCode();
        setlocale(LC_MONETARY, 'en_US');
    }

    public function setCode()
    {
        $this->code = (2**32) + (2**16) + (2**8) + (2**4) + (2**2) +2 +1;
    }

    public function getCode()
    {
        // return $this->code;
        return Auth::user()->code;
    }

    public function getSlug(Request $request)
    {
        if (isset($this->slug)) {
            $slug = $this->slug;
        } else {
            $slug = explode('.', $request->route()->getName())[1];
        }

        return $slug;
    }

    public function handleResponse(&$obj, $validator, $params){
        if ($validator->fails()) {
            return response()->json(['success' => false,'errors'=>$validator->errors(),'title_message' => 'Error en los datos ingresados'], 200);
        }else{
            $data = $obj->toSave($params);
            return response()->json(['success' => $data['success'],'title_message' =>$data['title_message'],'text' => $data['text']], 200);
        }
    }
}
