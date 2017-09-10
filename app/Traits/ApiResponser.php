<?php
namespace App\Traits;

use illuminate\Database\Eloquent\Model;
use illuminate\Support\Collection;

trait ApiResponser
{
	private function successResponse($data, $code)
	{
		return response()->json($data, $code);
	}
	protected function errorResponse($message, $code)
	{
		return response()->json(['error'=>$message, 'code'=>$code], $code);	
	}
	protected function showAll(Collection $collection, $code=200)
	{
		return response()->json(['data'=>$collection], $code);		
	}
	protected function showOne(Model $model, $code=200)
	{
		return response()->json(['data'=>$model], $code);					
	}
} 
