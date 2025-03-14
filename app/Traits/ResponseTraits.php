<?php
namespace App\Traits;
trait ResponseTraits
{
    public function errorResponse($message ,$error = "" , $status = 500)
    {
        return response()->json([
            "message" => $message,
            "error" => $error
        ],$status);
    }
    public function successResponse($message , $data = null , $status = 200){
        return response()->json([
            "message" => $message ,
            "data" => $data , 
            
        ],$status);
    }
  
}
