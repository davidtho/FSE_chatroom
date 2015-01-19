<?php

    $data = array();
    $function = $_POST['function'];
	
    if ($function == 'join') {
		$username = $_POST['username'];
		$joined = true;
		
		if(file_exists('users.txt')){
			$users = file('users.txt');
			foreach($users as $key => $user){
				if($key % 2 != 0){
					if (($username."\n") === ($user)){
						$joined = false;
					}
				}
			}
		}else{
			fwrite(fopen('users.txt','a'),"\n");
		}
		
		if ($joined){
			fwrite(fopen('users.txt', 'a'), $username."\n".time()."\n"); 
			$data['joined'] = true;
		}else{
			$data['joined'] = false;
		}
		
	}elseif ($function == 'update'){
		$newText = array();
		$username = $_POST['username'];
		if(file_exists('chatlog.txt')){
			$oldNumLines = $_POST['numLines'];
			$chatlog = file('chatlog.txt');
			$curNumLines = count($chatlog);
			$data['newNumLines'] = $curNumLines; 
			for ($lineNum = $oldNumLines; $lineNum < $curNumLines; $lineNum++){
				$newText[] = str_replace("\n","",$chatlog[$lineNum]);
				$data['newLines'] = $newText;
			}
		}else{
			$data['newNumLines'] = 0; 
			$data['newLines'] = 0;

		}
		
		$users = file('users.txt');
		$newUsers = array();
		$newUsers[] = "\n";
		$timeoutOccurred = 0;
		$ejected = true;
		
		foreach($users as $key => $user){
			if(str_replace("\n","",$user) === $username){
				$ejected = false;
			}
		}
		
		foreach($users as $key => $user){
			if($key % 2 != 0){
				$userTime = str_replace("\n","",$users[$key+1]);

				if ((time()-30) < ($userTime)){
					$newUsers[] = $user;
					$newUsers[] = $users[$key+1];
				}else{
					$timeoutOccurred = 1;
					if(str_replace("\n","",$user) === $username){
						$ejected = true;
					}
				}
			}
		}
		
		if($timeoutOccurred){
			$mode = 'w';
			foreach($newUsers as $curUser){
				fwrite(fopen('users.txt',$mode),  $curUser);
				$mode = 'a';
			}
		}
		
		$data['ejected'] = $ejected;
		
    }elseif ($function == 'send'){
		$username = $_POST['username'];
		$message = str_replace("\n","",$_POST['message']);
		
		$users = file('users.txt');
		//$mode = 'w';
		foreach($users as $key => $user){
			if($key % 2 != 0){
				if (str_replace("\n","",$user) === $username){
					$users[$key+1] = time()."\n";
				}
			}
			//fwrite(fopen('users.txt',$mode), $user);
			//$mode = 'a';
		}
		
		$mode = 'w';
		foreach($users as $key => $user){
			fwrite(fopen('users.txt',$mode), $user);
			$mode = 'a';
		}
		
		fwrite(fopen('chatlog.txt','a'), "<b>". $username."</b> <font color='gray'>(".date('m-d-Y H:i').")</font>: ".$message."\n");
		
	}elseif ($function == 'leave'){
		
		$username = $_POST['username'];
		$users = file('users.txt');
		$newUsers = array();
		$newUsers[] = "\n";
		
		foreach($users as $key => $curUser){
			if($key % 2 != 0){
				if(!(($username."\n") === $curUser)){
					$newUsers[] = $curUser;
					$newUsers[] = $users[$key + 1];
				}
			}
		}
		$mode = 'w';
		foreach($newUsers as $curUser){
			fwrite(fopen('users.txt',$mode),  $curUser);
			$mode = 'a';
		}
	}
	
	
	
    echo json_encode($data);

?>