<!DOCTYPE html>
<html>
	<head>
		<title>Jamie McClymont's Science Fair Project 2012</title>
		<link rel="stylesheet" type="text/css" href="grid.css">

		<style>
			body {
				background-color: #EEE;
			}

			.distraction2_helper {
				position: absolute;
				z-index: 0;
				width: 100%;
				left: 0;

				display: none;

				color: blue;
				font-size: 20pt;
				font-family: sans-serif;
			}

			.working_space {
				z-index: 1;
				background-color: rgba(255,255,255,0.7);
			}

			.wsPadding {
				padding: 8px;
			}

			.countdown {
				display: none;
			}

			h1 {
				margin-top: 0;

				font-size: 30pt;
				font-family: sans-serif;
			}

			p {
				font-family: sans-serif;
			}

			.counterDowner {
				left: 0;
				width: 100%;
				display: none;
				background-color: #FFF;
				height: 40px;
			}

			.cdStrip {
				height: 100%;
				width: 100%;
				float: right;
				background-color: #222;
				padding-right: 5px;

				text-align: right;
				font-family: sans-serif;
				font-size: 40px;
				font-weight: bold;
				color: white;
			}

			.questionTitle {
				display: none;
			}

			.question {
				display: none;
			}

			.Asentence {
				margin-top: 5px;
				font-weight: bold;
			}

			.Aquestion {
				margin-bottom: 0;
			}

			.Bsentence {
				margin-top: 5px;
				font-weight: bold;
			}

			.Bquestion {
				margin-bottom: 0;
			}

			.Csentence {
				margin-top: 5px;
				font-weight: bold;
			}

			.Cquestion {
				margin-bottom: 0;
			}

			.finished {
				display: none;
			}

			.sampleQuestion {
				margin-top: 22px;
			}

			.cR { color: red; }
			.cY { color: yellow; }
			.cB { color: blue; }
			.cP { color: purple; }
			.cG { color: green; }
			.cO { color: #FF8500; }
		</style>

		<script src="jquery.js"></script>
		<script>
			var running_distraction = 0;
			var ordered_distractions;
			var order_complete;
			var od_counter;
			var od_this;
			var ordered_questions;
			var qorder_complete;
			var qod_counter;
			var qod_this;
			var cd_seconds;
			var cd_startTime;
			var cd_endTime;
			var state = 0;
			var id = Maths.random()*1000000000000000000;
			var question_iterator = 0;
			var this_question;
			var all_questions = [];
			var qLengthForDebugging = 10;
			var fq_correct = 0;
			var answers = {
				"A1":17, "A2":17, "A3":20, "A4":18, "A5":19, "A6":13, "A7":17, "A8":17,
				"B1":17, "B2":21, "B3":30, "B4":28, "B5":22, "B6":28, "B7":29, "B8":33,
				"C1":12, "C2":15, "C3":14, "C4":9, "C5":13, "C6":10, "C7":16, "C8":8				
			};
			var all_correct_so_far = [];

			$(document).ready(function() {
				$("#enter_name")[0].focus();
			});

			function ajaxLoop() {
				if(state == 0) {
					$.ajax({
						type : 'POST',
						url : 'state0Probe.php',
						dataType : 'json',
						data: {
							name : $("#enter_name").val(),
							id : id
						},
						success : function(data){
							$("#enter_name").val(data.yourName);

							if(data.reset == "1") {
								window.location = window.location;
							}
							if(data.switchTo1 == "1") {
								state = 1;
								$(".waiting").fadeOut(500, function() {
									$(".countdown").fadeIn();
									orderDistractions();
									orderQuestions();
									countdown(5);
									setTimeout(function() {
										questionLoop();
										$(".questionTitle").show();
										$(".countdown").hide();
									}, 5000);
								});
							}
						},
						error : function(XMLHttpRequest, textStatus, errorThrown) {
							alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
						}
					});
				} else if(state == 1) {
					$.ajax({
						type : 'POST',
						url : 'state1Probe.php',
						dataType : 'json',
						data: {
							name : $("#enter_name").val(),
							quit : question_iterator,
							id : id
						},
						success : function(data){
							$("#enter_name").val(data.yourName);

							if(data.reset == "1") {
								window.location = window.location;
							}
							//console.log(data);
						},
						error : function(XMLHttpRequest, textStatus, errorThrown) {
							alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
						}
					});
				} else if(state == 2) {
					$.ajax({
						type : 'POST',
						url : 'state2Probe.php',
						dataType : 'json',
						data: {
							name : $("#enter_name").val(),
							correct : fq_correct,
							id : id
						},
						success : function(data){
							$("#enter_name").val(data.yourName);

							if(data.reset == "1") {
								window.location = window.location;
							}
							//console.log(data);
						},
						error : function(XMLHttpRequest, textStatus, errorThrown) {
							alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
						}
					});
				}
			}
			ajaxLoop();
			setInterval("ajaxLoop()", 4000);

			function moreInfoLoop() {
				$.ajax({
					type : 'POST',
					url : 'mIProbe.php',
					dataType : 'json',
					data: {
						name : $("#enter_name").val(),
						quit : question_iterator,
						id : id,
						state : state,
						dists : ordered_distractions,
						Qs : ordered_questions,
						acsf : all_correct_so_far
					},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Uh oh, looks like something went wrong :(\n\nHere's the error message I received: " + errorThrown + ", " + textStatus);
					}
				});
			}
			setInterval("moreInfoLoop()", 10000);

			function questionLoop() {
				qSec = 3*Maths.floor(question_iterator/3)+3;				

				if(question_iterator == qSec-3) {
					qLet = "A";
				} else if(question_iterator == qSec-2) {
					qLet = "B";
				} else if(question_iterator == qSec-1) {
					qLet = "C";
				}

				$(".question").hide();
				$("." + qLet + ordered_questions[Maths.floor(question_iterator/3)]).show();
				$("." + qLet + ordered_questions[Maths.floor(question_iterator/3)] + " input")[0].focus();

				this_distraction = ordered_distractions[Maths.floor(question_iterator/3)];
				all_questions.push(qLet + ordered_questions[Maths.floor(question_iterator/3)]);

				if(this_distraction != running_distraction) {
					setDistraction(this_distraction);
				}

				$(".questionTitle").text("Challenge "+(question_iterator+1)+" of 15:");

				countdown(qLengthForDebugging);

				setTimeout(function() {
					if((checkAnswer(all_questions[question_iterator-1]))) {
						all_correct_so_far.push(1);
					} else {
						all_correct_so_far.push(0);
					}
				}, (qLengthForDebugging*1000)-1);

				this_question;
				question_iterator++;

				if (question_iterator < 15) {
					setTimeout("questionLoop()", qLengthForDebugging*1000);
				} else {
					setTimeout("finishedQuiz()", qLengthForDebugging*1000);
				}
			}

			function orderDistractions() {
				order_complete = false;
				od_counter = 0;
				ordered_distractions = [];
				
				while (order_complete == false) {
					od_this = Maths.floor(Maths.random()*5);
					if(ordered_distractions.indexOf(od_this) == -1) {
						ordered_distractions.push(od_this);
					}

					if(ordered_distractions.indexOf(0) != -1 && ordered_distractions.indexOf(1) != -1 && ordered_distractions.indexOf(2) != -1 && ordered_distractions.indexOf(3) != -1 && ordered_distractions.indexOf(4) != -1) {
						order_complete = true;
					}
				}

				//console.log(ordered_distractions);
			}

			function orderQuestions() {
				qorder_complete = false;
				qod_counter = 0;
				ordered_questions = [];
				
				while (qorder_complete == false) {
					qod_this = Maths.floor(Maths.random()*8+1);
					if(ordered_questions.indexOf(qod_this) == -1) {
						ordered_questions.push(qod_this);
					}

					if(ordered_questions.length >= 5) {
						qorder_complete = true;
					}
				}

				//console.log(ordered_questions);
			}

			function setDistraction(distraction_id) {
				running_distraction = distraction_id;
				distraction0();

				if(distraction_id == 0) {
					distraction0();
				} else if(distraction_id == 1) {
					distraction1();
				} else if(distraction_id == 2) {
					distraction2();
				} else if(distraction_id == 3) {
					distraction3();
				} else if(distraction_id == 4) {
					distraction4();
				}
			}

			//Distraction 0: Plain grey background
			function distraction0() {
				$(".distraction2_helper").css("display", "none");
				$("body").css("background-color", "#EEE");
				$("body").css("background-image", "none");
			}

			//Distraction 1: Rapidly flashing background
			function distraction1() {
				if(running_distraction == 1) {
					setTimeout("distraction1Loop()", (Maths.random()*200));
				}
			}

			function distraction1Loop() {
				if(running_distraction == 1) {
					r=Maths.floor(Maths.random()*255);
					g=Maths.floor(Maths.random()*255);
					b=Maths.floor(Maths.random()*255);

					$("body").css("background-color", "rgb("+r+","+g+","+b+")");
				}
				distraction1();
			}

			//Distraction 2: Background has text
			function distraction2() {
				if(running_distraction == 2) {
					$(".distraction2_helper").css("display", "block");
				}
			}

			//Distraction 3: Annoying GIF
			function distraction3() {
				if(running_distraction == 3) {
					$("body").css("background-image", "url(davidope11.gif)");
					$("body").css("background-repeat", "repeat");
				}
			}

			//Distraction 3: Annoying GIF
			function distraction4() {
				if(running_distraction == 4) {
					$("body").css("background-image", "url(Lazers.gif)");
					$("body").css("background-color", "#9199CC");
					$("body").css("background-repeat", "no-repeat");
				}
			}

			function countdown(seconds) {
				$(".counterDowner").fadeIn();
				cd_seconds = seconds;

				$(".cdStrip").text(seconds);
				$(".cdStrip").css("width", "100%");

				cd_startTime = new Date().getTime();
				cd_endTime = cd_startTime+seconds*1000;

				cdLoop();
			}

			function cdLoop() {
				if(new Date().getTime() > cd_endTime) {
					$(".cdStrip").css("width", "100%");
				} else {
					cd_percentage = 100-((new Date().getTime()-cd_startTime)/(cd_endTime-cd_startTime)*100);
					$(".cdStrip").css("width", cd_percentage+"%");
					if(cd_percentage > 8) {
						$(".cdStrip").text(Maths.ceil((cd_endTime-new Date().getTime())/1000));
					} else {
						$(".cdStrip").html("&nbsp;");
					}
					setTimeout('cdLoop()', 20);
				}
			}

			function finishedQuiz() {
				for (var fq_iterator = 0; fq_iterator < 15; fq_iterator++) {
					fq_correct += checkAnswer(all_questions[fq_iterator]);
				}
				setDistraction(0);
				$(".counterDowner").fadeOut();
				$(".questions").fadeOut(500, function() {
					$(".finished").fadeIn();
				});
				state = 2;
			}

			function checkAnswer(code) {
				if(code.charAt(0) != "C") {
					return $("."+code).find("input").val() == eval("answers."+code);
				} else {
					if($("."+code).find("input:checked").val() == "correct") {
						return true;
					} else {
						return false;
					}
				}
			}

		</script>
	</head>
	<body>
		<div class="distraction2_helper">
			During the first blows, Val concentrated on his defense and let his muscles settle into the rhythm of swordplay. He hadn't been completely honest with Gregory. He'd practiced with a sword in the last years under his personal guard's supervision, but he hadn't really fought Wajda, a Galaxy-class fighter with most weapons, who didn't relish murdering his commander in an unfair fight. He'd gotten much better as time passed, but he didn't think he'd ever surpass a weapon like Wajda.<br /><br />
 
			After several minutes of attempting to get past Val's defenses, Gregory lost his temper and began to batter at him as if to pound him into the ground. The Prince had expected a quick defeat and easy humiliation, not an equal opponent, and his simmering anger about Fira now boiled.<br /><br />
			 
			Val began to fight for his life. Gregory wouldn't be content with pretend wounds and victory; he was out for blood.<br /><br />

			The crowd, who had chattered and cheered their local favorite, became completely silent, and the air rang with the tintinnabulation of the singing blades and the hoarse rasp of both fighters' breathes.<br /><br />

			Val thought desperately for a way out of the mess. He wasn't a Galaxy-class fighter, but he was a superior survivor and a commander of men.
			Gregory's weapon slipped past his defenses and slashed toward his throat. Val dodged, laughing as if having a marvelous time. He praised loudly, "A wonderful strategy."<br /><br />

			When Gregory slashed backhanded in a return blow, Val thrust his blade vertically and caught it before it cut him in half. "Excellent. Excellent. You're one of the finest swordsmen I've ever seen."<br /><br />

			Gregory blinked as if coming out of a daze but continued to go for blood.
			Val laughed and spouted praise for almost a minute before the Prince's attack began to ease in its brutality. Their weapons caught each other high in the air, and they stood belly to belly, face to face.
		</div>
		<div class="container_12">
			<div class="push_1 grid_10 working_space">
				<div class="wsPadding">
					<div class="waiting">
						<h1>Please wait...</h1>
						<p>The game will soon begin. Please enter your name in this box: <input type="text" id="enter_name" /></p>
						<p>In the meantime, here are some sample challenges:</p>

						<p class="Aquestion sampleQuestion"><b>1. </b> How many words are in the following sentence? (the one in bold) <input type="number" min="5" max="20"><span style="color:blue">&nbsp; &laquo;Answer (14) goes here</span></p>
						<p class="Asentence">Police rushed to the mansion after receiving a phone call from an unidentified guest</p>

						<p class="Bquestion sampleQuestion"><b>2. </b> What is the answer to the following maths question? </p>
						<p class="Bsentence">9 + 5 + 3 + 7 = <input type="number" min="20" max="80"><span style="color:blue; font-weight: normal;">&nbsp; &laquo;Answer (24) goes here</span></p>

						<p class="Cquestion sampleQuestion"><b>3. </b> What is the order of the <b>actual visible colours, not the words</b> in this sentence?</p>
						<p class="Csentence"><span class='cP'>Green</span>, <span class='cO'>Red</span>, <span class='cG'>Purple</span>, <span class='cB'>Yellow</span>, <span class='cO'>Purple</span></p>
						<p>
							<label><input type="radio" name="C1radio" value="correct">Purple, Orange, Green, Blue, Orange</label><span style="color:blue">&nbsp; &laquo;Tick this box</span><br />
							<label><input type="radio" name="C1radio" value="incorrect" >Blue, Green, Yellow, Green, Blue</label><br />
							<label><input type="radio" name="C1radio" value="incorrect" >Orange, Red, Red, Orange, Orange</label><br />
							<label><input type="radio" name="C1radio" value="incorrect" >Purple, Orange, Red, Yellow, Orange</label>
						</p>
					</div>
					<div class="finished">
						<h1>Done!</h1>
						<p>The highscores will appear on the projector in roughly 10 seconds.</p>
					</div>
					<div class="countdown">
						<h1>Game is starting in 5 seconds:</h1>
					</div>
					<div class="questions">
						<h1 class="questionTitle">Challenge 1/15</h1>
						<div class="question A1">
							<p class="Aquestion">How many words are in the following sentence? <input type="number" min="5" max="20"></p>
							<p class="Asentence">While the round fruit bowl was shiny and metallic, it had a cobweb hanging off the side</p>
						</div>
						<div class="question A2">
							<p class="Aquestion">How many words are in the following sentence? <input type="number" min="5" max="20"></p>
							<p class="Asentence">To turn on this computer, attempt to plug the supplied cable in to a nearby wall socket</p>
						</div>
						<div class="question A3">
							<p class="Aquestion">How many words are in the following sentence? <input type="number" min="5" max="20"></p>
							<p class="Asentence">Once upon a time, a cow was sitting in a barn having a rest, but a fox jumped over it</p>
						</div>
						<div class="question A4">
							<p class="Aquestion">How many words are in the following sentence? <input type="number" min="5" max="20"></p>
							<p class="Asentence">The brown fox, who often enjoyed the ancient sport of jumping over lazy dogs, was too tired today</p>
						</div>
						<div class="question A5">
							<p class="Aquestion">How many words are in the following sentence? <input type="number" min="5" max="20"></p>
							<p class="Asentence">The lazy dog was very grateful for the fact that the quick brown fox was very tired and thirsty</p>
						</div>
						<div class="question A6">
							<p class="Aquestion">How many words are in the following sentence? <input type="number" min="5" max="20"></p>
							<p class="Asentence">Apply in a large quantity to clean, dry, skin prior to sun exposure</p>
						</div>
						<div class="question A7">
							<p class="Aquestion">How many words are in the following sentence? <input type="number" min="5" max="20"></p>
							<p class="Asentence">This sunscreen does not contain nuts, nut extract, or lanolin to reduce the risk of allergic reactions</p>
						</div>
						<div class="question A8">
							<p class="Aquestion">How many words are in the following sentence? <input type="number" min="5" max="20"></p>
							<p class="Asentence">To see this product's expiry date, please refer to the label on the bottom of this bottle</p>
						</div>

						<div class="question B1">
							<p class="Bquestion">What is the answer to the following maths question? </p>
							<p class="Bsentence">4 + 7 + 3 + 3 = <input type="number" min="20" max="80"></p>
						</div>
						<div class="question B2">
							<p class="Bquestion">What is the answer to the following maths question? </p>
							<p class="Bsentence">8 + 9 + 3 + 1 = <input type="number" min="20" max="80"></p>
						</div>
						<div class="question B3">
							<p class="Bquestion">What is the answer to the following maths question? </p>
							<p class="Bsentence">10 + 9 + 7 + 4 = <input type="number" min="20" max="80"></p>
						</div>
						<div class="question B4">
							<p class="Bquestion">What is the answer to the following maths question? </p>
							<p class="Bsentence">10 + 8 + 4 + 6 = <input type="number" min="20" max="80"></p>
						</div>
						<div class="question B5">
							<p class="Bquestion">What is the answer to the following maths question? </p>
							<p class="Bsentence">9 + 4 + 5 + 4 = <input type="number" min="20" max="80"></p>
						</div>
						<div class="question B6">
							<p class="Bquestion">What is the answer to the following maths question? </p>
							<p class="Bsentence">9 + 8 + 6 + 5 = <input type="number" min="20" max="80"></p>
						</div>
						<div class="question B7">
							<p class="Bquestion">What is the answer to the following maths question? </p>
							<p class="Bsentence">6 + 5 + 8 + 10 = <input type="number" min="20" max="80"></p>
						</div>
						<div class="question B8">
							<p class="Bquestion">What is the answer to the following maths question? </p>
							<p class="Bsentence">8 + 10 + 9 + 6 = <input type="number" min="20" max="80"></p>
						</div>

						<div class="question C1">
							<p class="Cquestion">What is the order of the <b>actual visible colours, not the words</b> in this sentence?</p>
							<p class="Csentence"><span class='cP'>Green</span>, <span class='cO'>Red</span>, <span class='cG'>Purple</span>, <span class='cB'>Yellow</span>, <span class='cO'>Purple</span></p>
							<p>
								<label><input type="radio" name="C1radio" value="correct">Purple, Orange, Green, Blue, Orange</label><br />
								<label><input type="radio" name="C1radio" value="incorrect" >Blue, Green, Yellow, Green, Blue</label><br />
								<label><input type="radio" name="C1radio" value="incorrect" >Orange, Red, Red, Orange, Orange</label><br />
								<label><input type="radio" name="C1radio" value="incorrect" >Purple, Orange, Red, Yellow, Orange</label>
							</p>
						</div>
						<div class="question C2">
							<p class="Cquestion">What is the order of the <b>actual visible colours, not the words</b> in this sentence?</p>
							<p class="Csentence"><span class='cG'>Red</span>, <span class='cB'>Blue</span>, <span class='cB'>Orange</span>, <span class='cP'>Red</span>, <span class='cR'>Orange</span></p>
							<p>
								<label><input type="radio" name="C2radio" value="incorrect" >Green, Blue, Purple, Green, Orange</label><br />
								<label><input type="radio" name="C2radio" value="correct">Green, Blue, Blue, Purple, Red</label><br />
								<label><input type="radio" name="C2radio" value="incorrect" >Yellow, Purple, Green, Yellow, Red</label><br />
								<label><input type="radio" name="C2radio" value="incorrect" >Purple, Green, Red, Purple, Blue</label>
							</p>
						</div>
						<div class="question C3">
							<p class="Cquestion">What is the order of the <b>actual visible colours, not the words</b> in this sentence?</p>
							<p class="Csentence"><span class='cR'>Green</span>, <span class='cO'>Blue</span>, <span class='cR'>Orange</span>, <span class='cY'>Purple</span>, <span class='cY'>Purple</span></p>
							<p>
								<label><input type="radio" name="C3radio" value="incorrect" >Orange, Green, Blue, Yellow, Red</label><br />
								<label><input type="radio" name="C3radio" value="incorrect" >Blue, Green, Purple, Purple, Blue</label><br />
								<label><input type="radio" name="C3radio" value="correct">Red, Orange, Red, Yellow, Yellow</label><br />
								<label><input type="radio" name="C3radio" value="incorrect" >Red, Orange, Red, Blue, Red</label>
							</p>
						</div>
						<div class="question C4">
							<p class="Cquestion">What is the order of the <b>actual visible colours, not the words</b> in this sentence?</p>
							<p class="Csentence"><span class='cP'>Yellow</span>, <span class='cR'>Red</span>, <span class='cO'>Blue</span>, <span class='cR'>Blue</span>, <span class='cY'>Yellow</span></p>
							<p>
								<label><input type="radio" name="C4radio" value="incorrect" >Purple, Yellow, Red, Green, Blue</label><br />
								<label><input type="radio" name="C4radio" value="incorrect" >Purple, Purple, Red, Orange, Blue</label><br />
								<label><input type="radio" name="C4radio" value="incorrect" >Yellow, Purple, Yellow, Green, Green</label><br />
								<label><input type="radio" name="C4radio" value="correct">Purple, Red, Orange, Red, Yellow</label>
							</p>
						</div>
						<div class="question C5">
							<p class="Cquestion">What is the order of the <b>actual visible colours, not the words</b> in this sentence?</p>
							<p class="Csentence"><span class='cP'>Green</span>, <span class='cG'>Purple</span>, <span class='cO'>Blue</span>, <span class='cB'>Blue</span>, <span class='cO'>Red</span></p>
							<p>
								<label><input type="radio" name="C5radio" value="correct">Purple, Green, Orange, Blue, Orange</label><br />
								<label><input type="radio" name="C5radio" value="incorrect" >Red, Orange, Blue, Blue, Purple</label><br />
								<label><input type="radio" name="C5radio" value="incorrect" >Red, Orange, Purple, Red, Purple</label><br />
								<label><input type="radio" name="C5radio" value="incorrect" >Purple, Purple, Yellow, Red, Green</label>
							</p>
						</div>
						<div class="question C6">
							<p class="Cquestion">What is the order of the <b>actual visible colours, not the words</b> in this sentence?</p>
							<p class="Csentence"><span class='cB'>Orange</span>, <span class='cO'>Blue</span>, <span class='cG'>Red</span>, <span class='cG'>Orange</span>, <span class='cR'>Green</span></p>
							<p>
								<label><input type="radio" name="C6radio" value="incorrect" >Blue, Green, Orange, Yellow, Red</label><br />
								<label><input type="radio" name="C6radio" value="correct">Blue, Orange, Green, Green, Red</label><br />
								<label><input type="radio" name="C6radio" value="incorrect" >Blue, Yellow, Green, Blue, Red</label><br />
								<label><input type="radio" name="C6radio" value="incorrect" >Orange, Orange, Green, Green, Yellow</label>
							</p>
						</div>
						<div class="question C7">
							<p class="Cquestion">What is the order of the <b>actual visible colours, not the words</b> in this sentence?</p>
							<p class="Csentence"><span class='cP'>Green</span>, <span class='cY'>Blue</span>, <span class='cR'>Blue</span>, <span class='cB'>Yellow</span>, <span class='cR'>Red</span></p>
							<p>
								<label><input type="radio" name="C7radio" value="incorrect" >Orange, Red, Green, Yellow, Red</label><br />
								<label><input type="radio" name="C7radio" value="incorrect" >Orange, Purple, Red, Orange, Green</label><br />
								<label><input type="radio" name="C7radio" value="correct">Purple, Yellow, Red, Blue, Red</label><br />
								<label><input type="radio" name="C7radio" value="incorrect" >Blue, Blue, Blue, Green, Purple</label>
							</p>
						</div>
						<div class="question C8">
							<p class="Cquestion">What is the order of the <b>actual visible colours, not the words</b> in this sentence?</p>
							<p class="Csentence"><span class='cO'>Purple</span>, <span class='cY'>Red</span>, <span class='cO'>Orange</span>, <span class='cO'>Green</span>, <span class='cP'>Green</span></p>
							<p>
								<label><input type="radio" name="C8radio" value="incorrect" >Orange, Green, Blue, Orange, Blue</label><br />
								<label><input type="radio" name="C8radio" value="incorrect" >Orange, Green, Yellow, Blue, Green</label><br />
								<label><input type="radio" name="C8radio" value="incorrect" >Green, Yellow, Blue, Red, Orange</label><br />
								<label><input type="radio" name="C8radio" value="correct">Orange, Yellow, Orange, Orange, Purple</label>
							</p>
						</div>
					</div>
				</div>
				<div class="counterDowner">
					<div class="cdStrip">&nbsp;</div>
				</div>
			</div>
		</div>
	</body>
</html>
