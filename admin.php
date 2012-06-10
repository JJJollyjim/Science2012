<!DOCTYPE html>
<html>
	<head>
		<title>ADMIN</title>
		<style>
			table {
				width: 100%;
				border-collapse: collapse;
			}
			th, td {
				border: solid;
				border-width: 1px;
				padding: 0;
				width: 33%;
			}
			* {
				font-family: sans-serif;
			}
			h1 {
				text-align: center;
			}
		</style>
		<script src="jquery.js"></script>
		<script>
			function clearUsers(level) {
				$.ajax({
					type : 'POST',
					url : 'clearList.php',
					dataType : 'text',
					data: {
						level : level
					},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
					}
				});
				ajaxLoop();
			}
			function saveData() {
				$.ajax({
					type : 'POST',
					url : 'saveData.php',
					dataType : 'text',
					data: {
						groupId : prompt("Group Name?")
					}, success : function(data) {
						alert(data);
					}, error : function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
					}
				});
				ajaxLoop();
			}
			function toggleStarting() {
				$.ajax({
					type : 'POST',
					url : 'swapReady.php',
					dataType : 'text',
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
					}
				});
			}
			function toggleReset() {
				if(confirm("Are you SURE?!?!??!!???!!!?!??!?!?!??!?!")) {
					$.ajax({
						type : 'POST',
						url : 'swapReset.php',
						dataType : 'text',
						error : function(XMLHttpRequest, textStatus, errorThrown) {
							alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
						}
					});
				}
			}
			function toggleHs() {
				$.ajax({
					type : 'POST',
					url : 'swapHS.php',
					dataType : 'text',
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
					}
				});
			}
			function getInfo(user_id) {
				$.ajax({
					type : 'POST',
					url : 'getMI.php',
					dataType : 'json',
					data : {
						id : user_id
					},
					success : function(data) {
						alert("~~~USER INFO ALERT~~~\n\nID: "+user_id+"\nName: "+data.name+"\nState: "+data.state+"\nQuestion: "+data.quit+"\nOrdered Questions: "+data.Qs+"\nOrdered Distractions: "+data.dists+"\nAnswers correct so far: "+data.acsf+"\n");
					},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
					}
				});
			}
			function ajaxLoop() {
				$.ajax({
					type : 'GET',
					url : 'list.io',
					dataType : 'json',
					success : function(data){
						//console.log(data);
						$(".s0List").html("");
						for(key in data.state0) {
							if(data.state0[key] == "" || data.state0[key] == null) {
								data.state0[key] = "<i>No Name</i>"
							}
							$(".s0List").append("<li>"+data.state0[key]+" (<a href='javascript:getInfo("+key+")'>"+key+"</a>)</li>");
						}

						$(".s1List").html("");
						for(key in data.state1) {
							if(data.state1[key] == "" || data.state1[key] == null) {
								data.state1[key] = "<i>No Name</i>"
							}
							$(".s1List").append("<li>"+data.state1[key]+" (<a href='javascript:getInfo("+key+")'>"+key+"</a>, Q"+data.state1quit[key]+")</li>");
						}

						$(".s2List").html("");
						for(key in data.state2) {
							if(data.state2[key] == "" || data.state2[key] == null) {
								data.state2[key] = "<i>No Name</i>"
							}
							$(".s2List").append("<li>"+data.state2[key]+" (<a href='javascript:getInfo("+key+")'>"+key+"</a>, S"+data.state2score[key]+")</li>");
						}
					},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
					}
				});
			}
			ajaxLoop();
			setInterval("ajaxLoop()", 1000);
		</script>
	</head>
	<body>
		<h1>Science Project Admin Page</h1>
		<button onclick="javascript:clearUsers(0)">Clear</button>
		<button onclick="javascript:toggleStarting()">Toggle Starting</button>
		<button onclick="javascript:toggleHs()">Toggle Highscores</button>
		<button onclick="javascript:toggleReset()">Toggle Reset</button>
		<button onclick="javascript:saveData()">Save Data to Database</button>
		<button onclick="javascript:window.location='./client.php'">Open Client</button>
		<button onclick="javascript:window.location='./projector.php'">Open Projector</button>
		<button onclick="javascript:alert('KABOOM!!!!')">Self Destruct</button>
		<table>
			<thead>
				<tr>
					<th>Waiting Users <button onclick="javascript:clearUsers(1)">Clear</button></th>
					<th>Quizzing Users <button onclick="javascript:clearUsers(2)">Clear</button></th>
					<th>Finished Users <button onclick="javascript:clearUsers(3)">Clear</button></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<ul class="s0List">
							
						</ul>
					</td>
					<td>
						<ul class="s1List">
							
						</ul>
					</td>
					<td>
						<ul class="s2List">
							
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>