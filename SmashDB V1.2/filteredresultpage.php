<!DOCTYPE HTML>
<?php
	include_once("mysql_connection_smashdb.php");
?>
<html>
	
<html>
	<body>
		<form method = "post" action = "filteredresultpage.php">
			<?php
				$seasonResult = $_POST['SelectSeason'];
				$yearResult = $_POST['SelectYear'];
				$regionResult = $_POST['SelectRegion'];
				$locationResult = $_POST['SelectLocation'];
				$searchResult = $_POST['search'];
			?>
			<select name = "SelectSeason" style = "border-radius: 0.5em">
				<option value = 'Select Season'>Select Season</option>";	
				<?php
					$query = "SELECT DISTINCT name FROM season";
					$result = mysqli_query($connect, $query);
					while($mytuple = mysqli_fetch_assoc($result))
					{
						if($seasonResult == $mytuple['name'])
						{
							echo "<option selected value = '".$mytuple['name']."'>".$mytuple['name']."</option>";
						}
						else
						{
							echo "<option value = '".$mytuple['name']."'>".$mytuple['name']."</option>";
						}
					}
				?>
			</select>
			<select name = "SelectYear" style = "border-radius: 0.5em">
				<option value = 'Select Year'>Select Year</option>";	
				<?php
					$query = "SELECT DISTINCT year FROM season";
					$result = mysqli_query($connect, $query);
					while($mytuple = mysqli_fetch_assoc($result))
					{
						if($yearResult == $mytuple['year'])
						{
							echo "<option selected value = '".$mytuple['year']."'>".$mytuple['year']."</option>";
						}
						else
						{
							echo "<option value = '".$mytuple['year']."'>".$mytuple['year']."</option>";
						}
					}
				?>
			</select>
			<select name = "SelectRegion" style = "border-radius: 0.5em">
				<option value = 'Select Region'>Select Region</option>";	
				<?php
					$query = "SELECT DISTINCT region FROM location";
					$result = mysqli_query($connect, $query);
					while($mytuple = mysqli_fetch_assoc($result))
					{
						if($regionResult == $mytuple['region'])
						{
							echo "<option selected value = '".$mytuple['region']."'>".$mytuple['region']."</option>";
						}
						else
						{
							echo "<option value = '".$mytuple['region']."'>".$mytuple['region']."</option>";
						}
					}
				?>
			</select>
			<select name = "SelectLocation" style = "border-radius: 0.5em">
				<option value = 'Select Location'>Select Location</option>";	
				<?php
					if($locationResult == "Select Location")
					{
						$cityResult = "%";
						$stateResult = "%";
						$locationResult = "%";
					}
					else
					{
						$cityResult = strtok($locationResult,", ");
						$stateResult = 	ltrim(strtok("\n"));
					}
					$query = "SELECT DISTINCT city,state FROM location";
					$result = mysqli_query($connect, $query);
					while($mytuple = mysqli_fetch_assoc($result))
					{
						if($cityResult == $mytuple['city'] && $stateResult == $mytuple['state'])
						{
							echo "<option selected value = '".$mytuple['city'].", ".$mytuple['state']."'>".$mytuple['city'].", ".$mytuple['state']."</option>";
						}
						else
						{
							echo "<option value = '".$mytuple['city'].", ".$mytuple['state']."'>".$mytuple['city'].", ".$mytuple['state']."</option>";
						}
					}
				?>
			</select>
			<input type = "search" name = "search" placeholder = "Search for a player">
			</input>
			<input type = "submit" style = "border-radius: 0.5em; border: 2px solid #4286f4; background-color: #4faf9e;">
			<?php
				echo "<br>";
				if($seasonResult == "Select Season")
				{
					$seasonResult = "%";
				}
				echo "season: $seasonResult<br>";
				if($yearResult == "Select Year")
				{
					$yearResult = "%";
				}
				echo "year: $yearResult<br>";
				if($regionResult == "Select Region")
				{
					$regionResult = "%";
				}
				echo "region: $regionResult<br>";
				
				echo "location: $locationResult<br>";
				echo "city: $cityResult<br>";
				echo "state: $stateResult<br>";
				
				if($searchResult == "")
				{
					$searchResult = "%";
				}
				echo "search results for: $searchResult<br>";
			?>
		</form>
	<body>
	<?php				
		$went_in = false;
		
		$query =
			"SELECT name, year
			FROM season
			WHERE name LIKE '$seasonResult'
				AND year LIKE '$yearResult'";
		$result = mysqli_query($connect, $query); 
		
		//tuple is each season
		while($tuple = mysqli_fetch_assoc($result))
		{
			echo "<br>{$tuple['name']} ";
			echo "{$tuple['year']}<br>";
		
			$query1 =
				"SELECT state, city
				FROM hosted_tournament_in
				WHERE season_name = '{$tuple['name']}'
					AND season_year = '{$tuple['year']}'
					AND city LIKE '$cityResult'
					AND state LIKE '$stateResult'";
			$result1 = mysqli_query($connect, $query1);
			
			//tuple1 is each city, state that had tournaments during the season
			while($tuple1 = mysqli_fetch_assoc($result1))
			{
				echo "&emsp;&emsp;{$tuple1['state']}<br>";
				
				$query2 =
					"SELECT region
					FROM location
					WHERE state = '{$tuple1['state']}'
						AND city = '{$tuple1['city']}'
						AND region LIKE '$regionResult'";
				$result2 = mysqli_query($connect, $query2);
				
				//tuple2 is each region that had a tournament during the season
				while($tuple2 = mysqli_fetch_assoc($result2))
				{
					echo "&emsp;&emsp;&emsp;&emsp;{$tuple2['region']}<br>";
					
					$query3 =
						"SELECT city
						FROM location
						WHERE state = '{$tuple1['state']}'
							AND city = '{$tuple1['city']}'
							AND city LIKE '$cityResult'";
					$result3 = mysqli_query($connect, $query3);
					
					//tuple3 is each city that had a tournament in this season 
					while($tuple3 = mysqli_fetch_assoc($result3))
					{
						echo "&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{$tuple3['city']}<br>";
						
						$query4 =
							"SELECT is_from.player
							FROM is_from, playedduring
							WHERE is_from.city = '{$tuple1['city']}'
								AND is_from.state = '{$tuple1['state']}'
								AND playedduring.season_name = '{$tuple['name']}'
								AND playedduring.season_year = '{$tuple['year']}'
								AND is_from.player LIKE '$searchResult'";
						$result4 = mysqli_query($connect, $query4);
						
						//tuple4 is each player that went to a tournament
						//in this season and is from this city and state 
						while($tuple4 = mysqli_fetch_assoc($result4))
						{
							$went_in = true;
							echo "&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{$tuple4['player']}<br>";
							
							$query5 =
								"CREATE VIEW head_to_head AS
								SELECT play_in.player_one, play_in.player_two, sets.player_one_wins, sets.player_two_wins
								FROM play_in, sets
								WHERE play_in.player_one = '{$tuple4['player']}'
									AND play_in.set_id = sets.set_id

								UNION

								SELECT play_in.player_two, play_in.player_one, sets.player_two_wins, sets.player_one_wins
								FROM play_in, sets
								WHERE play_in.player_two = '{$tuple4['player']}'
									AND play_in.set_id = sets.set_id;";
								
							$result5 = mysqli_query($connect, $query5);
							
							$query5 =
								"SELECT player_one, player_two AS opponent, SUM(player_one_wins) AS player_one_total_wins, SUM(player_two_wins) AS opponent_wins
								FROM head_to_head
								GROUP BY player_one, player_two;";
	
							$result5 = mysqli_query($connect, $query5);
							
							while($tuple5 = mysqli_fetch_assoc($result5))
							{
								echo "&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;vs. {$tuple5['opponent']} {$tuple5['player_one_total_wins']} - {$tuple5['opponent_wins']}<br>";
							}
							
							$query5 = "DROP VIEW head_to_head;";
	
							$result5 = mysqli_query($connect, $query5);
						}
					}
				}
			}
		}
		
		
		if(!$went_in)
		{
			echo "<br><br>SORRY NO RESULTS FOUND FOR THE CRITERION!!!!!!<br>";
		}
		
		echo "<br>Hello, Daddy Sakurai";
	?>
<html>