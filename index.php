 <!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Chat Room</title>

	<style>
		#heading {color: #0000FF; font-size: 36px; font-weight: bold; 
			font-family:'Calibri';
			width: 400px;
			margin:auto
		}
		
		#ID_area {color: #0000FF; font-size: 22px; 
			font-family:'Calibri';
			width: 400px;
			margin:auto
		}
		
		#send_button {
			background-color: #0000FF;
			border-radius:6px;
			color: white;
			font-family: 'Calibri';
			font-size: 24px;
			text-decoration: none;
			cursor: pointer;
			border:none;
			width:400px;
			margin: auto;
		}

		#send_button:hover {
			border: none;
			background: #0000AA;
			box-shadow: 0px 0px 1px #777;
		}

		#send_button:active {
			border: none;
			background: #000055;
			box-shadow: 0px 0px 1px #777;
		}
		
		#logout_button {
			background-color: #0000FF;
			border-radius:6px;
			color: white;
			font-family: 'Calibri';
			font-size: 20px;
			text-decoration: none;
			cursor: pointer;
			border:none;
			width:80px;
			margin: auto;
		}

		#logout_button:hover {
			border: none;
			background: #0000AA;
			box-shadow: 0px 0px 1px #777;
		}

		#logout_button:active {
			border: none;
			background: #000055;
			box-shadow: 0px 0px 1px #777;
		}
		
		#chat_area {border: 2px solid black; height: 400px; width: 400px; 
			background-color: #FFFFFF;
			font-family:'Calibri';
			overflow: auto; 
			margin: auto
		}
		
		#input_form {width: 400px; margin: auto;}
		
		#input_message {width: 394px; margin: auto;
			border: 2px solid black;
			resize: none
		}
		
		#app_area {border: 2px solid black; width: 500px; 
			background-color: #EEEEEE;
			margin: auto;
		}
	</style>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script>
		
		var joined = false;
		var numLines = 0;
		
		var chatroom = (function(){

			function join(username, callback) {
			
				if (username.length > 15 || username.length < 1){
					callback(false);
				}else{
					$.ajax({
						type: "post", 
						url: "serverside.php",
						dataType: "json",
						data: {'function':'join','username':username},
						success: function(data){
							joined = data.joined;
							callback(joined);
						}
					});
				}
			}
			
			function update() {
				if(joined){
					$.ajax({
						type: "post", 
						url: "serverside.php",
						dataType: "json",
						data: {'function':'update','numLines':numLines,'username':username},
						success: function(data){
							if (data.ejected){
								alert("You have been logged out.");
								location.reload();
							}
										
							var chatArea = document.getElementById("chat_area");
							if(data.newLines){
								newLines = data.newLines;
								var chatArea = document.getElementById("chat_area");
								for(var i = 0; i < newLines.length; i++){
									chatArea.innerHTML = chatArea.innerHTML + newLines[i] + "</br></br>";
								}
								chatArea.scrollTop = chatArea.scrollHeight;
								numLines = data.newNumLines;
							}
						}
					});
				}
			}
			
			function send(message, username) {
				$.ajax({
					type: "post", 
					url: "serverside.php",
					dataType: "json",
					data: {'function':'send','message':message,'username':username},
					success: function(){
						update();
					}
				});
			}
			
			function leave(username) {
				$.ajax({
					type: "post", 
					url: "serverside.php",
					dataType: "json",
					data: {'function':'leave','username':username},
					success: function(){
						location.reload();
					}
				});
			}
			
			return { 
				join: join,
				update: update,
				send: send,
				leave: leave
			};
		})();
		
	</script>
</head>

<body>
	<div id = "app_area">
		<p id = "heading">FSE Chat Room</p>
		
		<p id = "ID_area">Username: <span id = "name" style="color:red"></span> <button id = "logout_button" type = "button">Logout</button> </p>
		</br>
		<div id = "chat_area"></div>
		</br>
		<form id = "input_form">
			<textarea id = "input_message"></textarea>
			</br>
			<button id = "send_button" type = "button" form = "input_form" value = "Submit">Send</button>
		</form>
		
		</br>
	</div>
	
	<script>
	
		
		var username = prompt("Enter a username. Must be unused.");
		if (username == null){
			location.reload();
		}
		username = username.replace(/\s+/g, '');
		username = username.replace('\n','');
		
		function callback(joined) {
			if(!joined){
				username = prompt("Try again.");
				if (username == null){
					location.reload();
				}
				username = username.replace(/\s+/g, '');
				username = username.replace('\n','');
				chatroom.join(username, callback);
			}else{
				document.getElementById("name").innerHTML = username;
				chatroom.update();
			}
		}
		
		chatroom.join(username, callback);

		setInterval(chatroom.update, 2000);
		
		document.getElementById("input_message").onkeydown = function(event){	
				if(event.key == 'Enter'){
					event.preventDefault();
				}
			};
		
		document.getElementById("input_message").onkeyup = function(event){
				if((event.key == 'Enter') & joined){	
					var message = document.getElementById("input_message").value;
					if (message.length > 160){
						alert("Message too long(" + message.length + " characters). Maximum 160 allowed.");
					}else{
						chatroom.send(message, username);
						document.getElementById("input_message").value = '';
					}
				}
			};
		
		document.getElementById("send_button").onclick = (function() {
			if(joined){
				var message = document.getElementById("input_message").value;	
				if (message.length > 160){
					alert("Message too long(" + message.length + " characters). Maximum 160 allowed.");
				}else{
					chatroom.send(message, username);
					document.getElementById("input_message").value = '';
				}
			}
		});		
		
		document.getElementById("logout_button").onclick = (function() {
			if(joined){
				chatroom.leave(username);
			}
		});
		
	
	</script>
	
	
</body>