<?php

function son_response($content = "", $status = 200, Array $headers = [])
{
	$factory = new App\Http\ResponseFactory();

	if(func_num_args() === 0){
		return $factory;
	}

	return $factory->make($content, $status, $headers);
}