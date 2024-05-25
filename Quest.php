<?php 
	ini_set('display_errors', 1);  // エラー表示を有効
	ini_set('error_reporting', E_ALL);  // エラー出力の範囲を指定
	ini_set('error_log', 'error.log');  // エラーログの保存先

	try{
		$pdo = new PDO(
		'mysql:dbname=TarkovItemManager;host=localhost;charset=utf8mb4',
		'webapp',
		'passwd',
		[
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
			PDO::ATTR_EMULATE_PREPARES => false,
		]
		);

		if(isset($_POST['button_plus'])){
			$input = explode('_', $_POST['button_plus']);
			$tmp = $pdo->prepare("SELECT current_quantity FROM Item WHERE name = ? AND FiR = ?");
			$tmp->bindValue(1,$input[0], PDO::PARAM_STR);
			$tmp->bindValue(2,$input[1], PDO::PARAM_STR);
			$tmp->execute();
			$tmpRow = $tmp->fetch();
			if($tmpRow[0] < $input[2]){
				$count = $pdo->prepare("UPDATE Item SET current_quantity = current_quantity + 1 WHERE name = ? AND FiR = ?");
				$count->bindValue(1,$input[0], PDO::PARAM_STR);
				$count->bindValue(2,$input[1], PDO::PARAM_STR);
				$count->execute();
			}
		}
		
		if(isset($_POST['button_minus'])){
			$input = explode('_', $_POST['button_minus']);
			$tmp = $pdo->prepare("SELECT current_quantity FROM Item WHERE name = ? AND FiR = ?");
			$tmp->bindValue(1,$input[0], PDO::PARAM_STR);
			$tmp->bindValue(2,$input[1], PDO::PARAM_STR);
			$tmp->execute();
			$tmpRow = $tmp->fetch();
			if($tmpRow[0] > 0){
				$count = $pdo->prepare("UPDATE Item SET current_quantity = current_quantity - 1 WHERE name = ? AND FiR = ?");
				$count->bindValue(1,$input[0], PDO::PARAM_STR);
				$count->bindValue(2,$input[1], PDO::PARAM_STR);
				$count->execute();
			}
		}

		if(!empty($_POST['inputText'])){
			$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND s.name LIKE ? AND s.FiR = i.FiR");
			$str = "%";
			$str .= $_POST['inputText'];
			$str .= "%";
			$stmt->bindValue(1,$str, PDO::PARAM_STR);
		}else if(isset($_POST['submit'])) {
			$Hideout = isset($_POST['Hideout']) ? 1 : 0;
			$Collector = isset($_POST['Collector']) ? 1:0;
			$Other = isset($_POST['Other']) ? 1:0;
			$FiR = isset($_POST['FiR']) ? 1:0;

			if(!$Hideout && !$Collector && !$Other && $FiR){ //0001
				$stmt = $pdo->prepare("SELECT s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND s.FiR = 'Yes' AND i.FiR = 'Yes'");
			}else if(!$Hideout && !$Collector && $Other && !$FiR){	//0010
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND attribute = 'Other' AND s.FiR = 'No' AND i.FiR = 'No'");
			}else if(!$Hideout && !$Collector && $Other && $FiR){	//0011
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND attribute = 'Other' AND s.FiR = 'Yes' AND i.FiR = 'Yes'");
			}else if(!$Hideout && $Collector && !$Other && !$FiR){	//0100
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND attribute = 'Collector' AND s.FiR = 'No' AND i.FiR = 'No'");
			}else if(!$Hideout && $Collector && !$Other && $FiR){	//0101
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND attribute = 'Collector' AND s.FiR = 'Yes' AND i.FiR = 'Yes'");
			}else if(!$Hideout && $Collector && $Other && !$FiR){	//0110
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND (attribute = 'Collector' OR attribute = 'Other') AND s.FiR = 'No' AND i.FiR = 'No'");
			}else if(!$Hideout && $Collector && $Other && $FiR){	//0111
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND (attribute = 'Collector' OR attribute = 'Other') AND s.FiR = 'Yes' AND i.FiR = 'Yes'");
			}else if($Hideout && !$Collector && !$Other && !$FiR){	//1000
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND attribute = 'Hideout' AND s.FiR = 'No' AND i.FiR = 'No'");
			}else if($Hideout && !$Collector && !$Other && $FiR){	//1001
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND attribute = 'Hideout' AND s.FiR = 'Yes' AND i.FiR = 'Yes'");
			}else if($Hideout && !$Collector && $Other && !$FiR){	//1010
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND (attribute = 'Hideout' OR attribute = 'Other') AND s.FiR = 'No' AND i.FiR = 'No'");
			}else if($Hideout && !$Collector && $Other && $FiR){	//1011
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND (attribute = 'Hideout' OR attribute = 'Other') AND s.FiR = 'Yes' AND i.FiR = 'Yes'");
			}else if($Hideout && $Collector && !$Other && !$FiR){	//1100
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND (attribute = 'Hideout' OR attribute = 'Collector') AND s.FiR = 'No' AND i.FiR = 'No'");
			}else if($Hideout && $Collector && !$Other && $FiR){	//1101
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND (attribute = 'Hideout' OR attribute = 'Collector') AND s.FiR = 'Yes' AND i.FiR = 'Yes'");
			}else if($Hideout && $Collector && $Other && !$FiR){	//1110
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND (attribute = 'Hideout' OR attribute = 'Collector' OR attribute = 'Other') AND s.FiR = 'No' AND i.FiR = 'No'");
			}else if($Hideout && $Collector && $Other && $FiR){	//1111
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND (attribute = 'Hideout' OR attribute = 'Collector' OR attribute = 'Other') AND s.FiR = 'Yes' AND i.FiR = 'Yes'");
			}else{
				$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND (attribute = 'Hideout' OR attribute = 'Collector' OR attribute = 'Other') AND s.FiR = i.FiR");
			}
		}else{
			$stmt = $pdo->prepare("SELECT  s.name, s.FiR, s.required_quantity, i.current_quantity FROM Search s INNER JOIN Item i ON s.name = i.name AND (attribute = 'Hideout' OR attribute = 'Collector' OR attribute = 'Other') AND s.FiR = i.FiR");
		}

		$stmt->execute();

		

	}catch(PDOException $e){
		header('Content-Type: text/plain; charset=UTF-8', true, 500);
		exit($e->getMessage());
	}

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="quest.css">
</head>
<body>

	<div class="container">
		<div class="top">

			<div class="title">
				<a href="top.html">
					<p>Item Management</p>
				</a>
			</div>
			
				<form class="form_quest" method="POST" action=" ">
					<div class="input">
						<input type="text" class="input_text" placeholder="Search Item Name" name="inputText"/> 
						<input type="submit" class="button_submit" name="submit" value="submit"/>
					</div>

					<div class="boxes">
						<input type="checkbox" id="box-1" class="checkbox" name="Hideout" value="1" <?= (!empty($Hideout) && $Hideout) ? "checked" : ""?>>
						<label for="box-1">Hideout</label>

						<input type="checkbox" id="box-2" class="checkbox" name="Collector" value="1" <?= (!empty($Collector) && $Collector) ? "checked" : ""?>>
						<label for="box-2">Collector</label>

						<input type="checkbox" id="box-3" class="checkbox" name="Other" value="1" <?= (!empty($Other) && $Other) ? "checked" : ""?>>
						<label for="box-3">Other Task</label>

						<input type="checkbox" id="box-4" class="checkbox" name="FiR" value="1" <?= (!empty($FiR) && $FiR) ? "checked" : ""?>>
						<label for="box-4">Find in Raid</label>
					</div>

				</form>

			<a href="Unlock.html">
				<div class="container_trade">
					<p>Unloking Items</p>
					<img src="images/Mechanic.png">
				</div>
			</a>
		</div>

		<div class="content">
			<?php 
				while($row = $stmt->fetch()){
					echo "<div class='panel'>";
						echo "<div class='left'>";
							echo "<p>$row[0]</p>";
							echo "<img src='items/$row[0].png'>";
							if($row[1] == "Yes"){
								echo "<div class='check'>";
								echo "<img src='images/check.png'>";
								echo "</div>";
							}
						echo "</div>";
						echo "<div class='right'>";
							echo "<form method='POST' action=' '>";
								echo "<input type='submit' class='button_minus' name='button_minus' value='$row[0]_$row[1]_$row[2]'>";
							echo "</form>";
							echo "<p>$row[3]/$row[2]</p>";
							echo "<form method='POST' action=' '>";
								echo "<input type='submit' class='button_plus' name='button_plus' value='$row[0]_$row[1]_$row[2]'>";
							echo "</form>";
						echo "</div>";
					echo "</div>";
				}
			?>

		</div>

	</div>
	
</body>
</html>

