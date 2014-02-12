<?php

class Admin {

	function index(){

		return (new View('editor'))->render(false, false, false);
		exit('TODO: create a Admin Area here!');
	}


}