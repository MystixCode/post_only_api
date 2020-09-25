<?php
	function is_valid($type, $data)
	{
		switch($type)
		{
			case 'alpha':
				$regex="/^[a-z]+$/i"; 						#a-z
				break;
			case 'alphanumeric':
				$regex="/^[a-z0-9]+$/i";					#a-z 0-9
				break;
			case 'numeric':
				$regex="/^[0-9]+$/i";							#0-9
				break;
			case 'alphanumeric_s1':
				$regex="/^[a-z0-9.,!?:_@$]+$/i";	#a-z 0-9 .,!?:_@$
				break;
			case 'alphanumeric_s2':
				$regex="/^[a-z0-9 _-]+$/i";				#a-z 0-9  _-
				break;
			case 'alphanumeric_s3':
				$regex="/^[a-z0-9_-]+$/i";				#a-z 0-9 _-
				break;
			case 'alphanumeric_s4':
				$regex="/^[a-z0-9.\/\*]+$/i";			#a-z 0-9 ./*  oderso ähnlich
				break;
		}
		if (preg_match($regex,$data))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
?>
