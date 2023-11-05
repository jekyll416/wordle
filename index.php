<?php ini_set("display_errors",true);?><html>
	<head>
		<link rel="stylesheet" href="/wordle/styles.css">
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	</head>
	<body>
		<div class="container"><?php 

	define("GUILD",$_GET['guild']);
	/* The database connection needs to come after GUILD is set so that the page connects to the correct database */
	require_once("connect.php");
	
	if(isset($_REQUEST['user'])){
		define("USER",$_GET['user']);
		echo "<div class=\"card hero\">";
		$sql="Select *, (select AVG(score) from games where games.user_id = users.id and score!='X') as avg from users where id='".$_REQUEST['user']."'";
		if($qry=$db->query($sql))
		{
			$user=$qry->fetch_assoc();
			$user_id=$user['id'];
			$average = $user['avg'];
			echo "<h1 style=\"\"><img class=\"avatar\" src=\"".$user['avatar']."\"> <div>".$user['username']."</div></h1>";
		}else{
			echo mysqli_error($db);
		}
		echo "</div>";
		echo "<div class=\"breadcrumb\"><a href=\"/wordle/".GUILD."/\">Home</a> / <a href=\"/wordle/".GUILD."/".USER."\">".$user['username']."</a></div>";
		$sql="Select * from earned_achievements ea
		Join wordle.achievements wa on wa.id=ea.achievement_id
		WHERE ea.user_id='".$_REQUEST['user']."'";
		if($qry=$db->query($sql))
		{
			echo "<div class=\"card\">";
			while($row=$qry->fetch_assoc())
			{
				echo "<div class=\"row\"><span>".$row['achievement_name']."</span><span>".$row['emot']."</span></div>";
			}
			echo "</div>";
		}else{
			echo mysqli_error($db);
		}
		
		$guesses=array("1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0,"6"=>0,"X"=>0);
		$sql="Select score, Count(*) as count from games where user_id='".$user_id."' group by score order by score";

		if($qry=$db->query($sql)){
			
			while($row = $qry->fetch_assoc())
			{
				$guesses[$row['score']]=$row['count'];
			}
		} ?>
		<div class="card">
		<?php 
			include("chart.php");
			$chart = new Chart($guesses);
			$chart->draw();
			
		 ?>
		</div> 
		<div class="card">
			<?php
				$played_games=array();
				$sql= " Select * from games  where user_id='".USER."' order by game_number asc";
				if($games=$db->query($sql)){
					while($game = $games->fetch_assoc())
					{
						$solutions[$game['game_number']]=$game['solution'];
						$played_games[]=$game['game_number'];
					}
				}
				$currentStreak = 0;
				$longestStreak = 0;
				$prevGameNumber = null;
				foreach ($played_games as $game) {
				    $gameNumber = $game;
				
				    // Check if it's the first game or consecutive to the previous one
				    if ($prevGameNumber === null || $gameNumber == $prevGameNumber + 1) {
				        $currentStreak++;
				    } else {
				        $currentStreak = 1; // Reset streak if not consecutive
				    }
				
				    if ($currentStreak > $longestStreak) {
				        $longestStreak = $currentStreak;
				    }
				
				    $prevGameNumber = $gameNumber;
				}

			?>
			<div class="row"><span>Games Recorded:</span><span><?php print sizeof($played_games);?></span></div>
			<div class="row"><span>Average Score:</span><span><?php print round($average,3);?></span></div>
			<div class="row"><span>Longest Streak:</span><span><?php print $longestStreak;?></span></div>
			<div class="row"><span>Current Streak:</span><span><?php print $currentStreak;?></span></div>
		</div>
		<?php
/*
		}else{
			echo mysqli_error($db);
		}
*/
		
		if(!empty($solutions)){
			echo "<div class=\"card puzzles\" style=\"grid-column:1/-1\">";
			foreach($solutions as $game_number=>$solution)
			{
 				echo "<div class=\"puzzle\">".$game_number."\n".$solution."</div>";
			}
			echo "</div>";
		}
	
	}else{
		
		echo "<div class=\"card hero\"><h1><img class=\"avatar\" src=\"/wordle/alpha-wordle-icon-new-square320-v3.gif\">Wordle Bot</h1></div>";
		echo "<div class=\"breadcrumb\">Home</div>";
			$sql="Select *, (select AVG(score) from games where games.user_id = users.id and score!='X') as avg from users order by avg asc";
	$qry=$db->query($sql);
	echo "<div class=\"card\"><div class=\"card-title\">Users</div>";
	while($row = $qry->fetch_assoc())
	{
		echo "<a  href=\"/wordle/".GUILD."/".$row['id']."\" class=\"row\">
			<div>
				<img class=\"avatar\" src=\"".$row['avatar']."\">
			</div>
			<div style=\"text-align:right;\">
				".$row['username']."<br>
				<span class=\"label\">avg. ".round($row['avg'],2)."</span>
			</div>";
		$sql="Select wa.emot from earned_achievements ea 
		JOIN wordle.achievements wa on ea.achievement_id = wa.id
		WHERE ea.user_id = '".$row['id']."'";
		$emot_query = $db->query($sql);
		if($emot_query->num_rows)
		{
			echo "<div class=\"emots\">";
			while($achieve = $emot_query->fetch_assoc()){ echo $achieve['emot']." ";}
			echo "</div>";
		
		}
		echo "</a>";
	}
	echo "</div>";
/*	$sql="Select * from games JOIN `users` on users.id=games.user_id order by game_number Desc limit 8";
	$qry=$db->query($sql);

	echo "<div class=\"card\"><div class=\"card-title\">Recent Games</div>";
	while($row = $qry->fetch_assoc())
	{
		echo "<div class=\"row\"><div>".$row['username']."</div><div>#".$row['game_number']."</div><div class=\"solution\">".$row['solution']."</div></div>";
	}
	echo "</div>";
*/
	$sql="Select * from earned_achievements ea
	JOIN `users` on users.id=ea.user_id 
	JOIN wordle.achievements wa on wa.id =  ea.achievement_id
	order by ea.id Desc limit 8";
	$qry=$db->query($sql);
	echo mysqli_error($db);
	echo "<div class=\"card\"><div class=\"card-title\">Recent Achievements</div>";
	while($row = $qry->fetch_assoc())
	{
		echo "<div class=\"row\"><div>".$row['username']."</div><div class=\"solution\">".$row['achievement_name']."</div></div>";
	}
	echo "</div>";
	
	$guesses=array("1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0,"6"=>0, "X"=>0);

	$sql="Select score, Count(*) as count from games group by score order by score";
	if($qry=$db->query($sql)){
		while($row = $qry->fetch_assoc())
		{
			$guesses[$row['score']]=$row['count'];
		}
	}else{
		echo mysqli_error($db);
	}

	?>
	
<!--
	<div class="card" style="padding:5px;">
	  <canvas id="myChart" style="margin:auto"></canvas>
	</div>
-->
	<script>
  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['1', '2', '3', '4', '5', '6'],
      datasets: [{
        label: '# of Guesses',
        data: [<?php print implode(", ",$guesses);?>],
        borderWidth: 1
      }]
    },
    options: {
	    indexAxis:'y',
	    responsive: true,
	    aspectRatio:1,
		scales: {
			y: {
				grid:{
					display:false
				},
				ticks:{
					color:"#000"
				}
			},
			x: {
				grid: {
					color:['#333']	
				},
				ticks: {
					color:'#000',
					callback: function(value, index, ticks){
						return Math.floor(value)==value?value:'';
					}
				}
			}
      	}
    }
  });
</script>
		<div class="card">
		<?php include("chart.php");
			$chart = new Chart($guesses);
			
			$chart->draw();
			?>
		</div>
				<?php } ?>
		

		</div>

		
		</body>
</html>