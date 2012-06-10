<!DOCTYPE html>
<html>
	<head>
		<title>Science Project</title>
		<style>
			body {
				background-color: #000;
				text-align: center;
			}

			* {
				font-family: sans-serif;
				color: #FFF;
			}

			h1 {
				font-size: 50pt;
			}

			table {
				width: 100%;
				border-collapse: collapse;
				background-color: #FFF;
			}

			th, td {
				border: solid;
				border-width: 1px;
				padding: 0;
				width: 33%;
				color: #000;
				font-size: 25pt;
			}

			td {
				border: solid;
				border-width: 1px;
				padding: 0;
				width: 33%;
				color: #000;
				font-size: 20pt;
			}

			i {
				color: #555;
			}

			.state0, .state1, .state2 {
				display: none;
			}
		</style>

		<script src="jquery.js"></script>
		<script>
			function ajaxLoop() {
				$.ajax({
					type : 'POST',
					url : 'proProbe.php',
					dataType : 'json',
					success : function(data){
						if(data.state == 0) {
							$(".state2").fadeOut();
							$(".state1").fadeOut(500, function() {
								$(".state0").fadeIn();
							});
						} else if(data.state == 1) {
							$(".state2").fadeOut();
							$(".state0").fadeOut(500, function() {
								$(".state1").fadeIn();
							});
						} else if(data.state == 2) {
							$("tbody").html("");

							for(var i=0;i<10;i++) {
								var tr = "";

								if(data.hsNames[i] == null || data.hsNames[i] == "") {
									data.hsNames[i] = "<i>No Name Entered</i>";
								}

								tr += "<tr>";
								tr += "<td>";
								tr += (i+1)+".";
								tr += "</td>";
								tr += "<td>";
								tr += data.hsNames[i];
								tr += "</td>";
								tr += "<td>";
								tr += data.hsScores[i]+"/15";
								tr += "</td>";
								tr += "</tr>";

								if(data.hsScores[i] != null) {
									$("tbody").append(tr);
								}
							}

							$(".state0").fadeOut();
							$(".state1").fadeOut(500, function() {
								$(".state2").fadeIn();
							});
						}
					}
				});
			}
			ajaxLoop();
			setInterval("ajaxLoop()", 1000);
		</script>
	</head>
	<body>
		<div class="state0"><h1>Game will soon begin...</h1></div>
		<div class="state1"><h1><br />Game in progress...</h1></div>
		<div class="state2">
			<h1>Highscores:</h1>
			<table>
			<thead>
				<tr>
					<th>#</th>
					<th>Name:</th>
					<th>Score:</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1. </td>
					<td>Jamie McClymont</td>
					<td>14/15</td>
				</tr>
			</tbody>
		</table>
		</div>
	</body>
</html>