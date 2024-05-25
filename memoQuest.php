<?php 
	ini_set('display_errors', 1);  // エラー表示を有効
	ini_set('error_reporting', E_ALL);  // エラー出力の範囲を指定
	ini_set('error_log', 'error.log');  // エラーログの保存先

	$Hideout = 0;
	$Collector = 0;
	$Other = 0;
	$FiR = 0;

	file_put_contents('received_data.log', print_r($Hideout, true), FILE_APPEND);
	file_put_contents('received_data.log', print_r($Collector, true), FILE_APPEND);
	file_put_contents('received_data.log', print_r($Other, true), FILE_APPEND);
	file_put_contents('received_data.log', print_r($FiR, true), FILE_APPEND);

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

		if(isset($_POST['name']) && isset($_POST['value'])) {
			switch($_POST['name']){
				case "Hideout":
					$Hideout = $_POST['value'];
					break;
				case "Collector":
					$Collector = $_POST['value'];
					break;
				case "Other":
					$Other = $_POST['value'];
					break;
				case "FiR":
					$FiR = $_POST['value'];
					break;
			}
			if(!$Hideout && !$Collector && !$Other && $FiR){ //0001
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE FiR = 'Yes'");
			}else if(!$Hideout && !$Collector && $Other && !$FiR){	//0010
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE attribute = 'Other' AND FiR = 'No'");
			}else if(!$Hideout && !$Collector && $Other && $FiR){	//0011
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE attribute = 'Other' AND FiR = 'Yes'");
			}else if(!$Hideout && $Collector && !$Other && !$FiR){	//0100
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE attribute = 'Collector' AND FiR = 'No'");
			}else if(!$Hideout && $Collector && !$Other && $FiR){	//0101
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE attribute = 'Collector' AND FiR = 'Yes'");
			}else if(!$Hideout && $Collector && $Other && !$FiR){	//0110
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE (attribute = 'Collector' OR attribute = 'Other') AND FiR = 'No'");
			}else if(!$Hideout && $Collector && $Other && $FiR){	//0111
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE (attribute = 'Collector' OR attribute = 'Other') AND FiR = 'Yes'");
			}else if($Hideout && !$Collector && !$Other && !$FiR){	//1000
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE attribute = 'Hideout' AND FiR = 'No'");
			}else if($Hideout && !$Collector && !$Other && $FiR){	//1001
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE attribute = 'Hideout' AND FiR = 'Yes'");
			}else if($Hideout && !$Collector && $Other && !$FiR){	//1010
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE (attribute = 'Hideout' OR attribute = 'Other') AND FiR = 'No'");
			}else if($Hideout && !$Collector && $Other && $FiR){	//1011
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE (attribute = 'Hideout' OR attribute = 'Other') AND FiR = 'Yes'");
			}else if($Hideout && $Collector && !$Other && !$FiR){	//1100
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE (attribute = 'Hideout' OR attribute = 'Collector') AND FiR = 'No'");
			}else if($Hideout && $Collector && !$Other && $FiR){	//1101
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE (attribute = 'Hideout' OR attribute = 'Collector') AND FiR = 'Yes'");
			}else if($Hideout && $Collector && $Other && !$FiR){	//1110
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE (attribute = 'Hideout' OR attribute = 'Collector' OR attribute = 'Other') AND FiR = 'No'");
			}else if($Hideout && $Collector && $Other && $FiR){	//1111
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE (attribute = 'Hideout' OR attribute = 'Collector' OR attribute = 'Other') AND FiR = 'Yes'");
			}else{
				$stmt = $pdo->prepare("SELECT name FROM Search WHERE attribute = 'none' AND FiR = 'No'");
			}

			$stmt->execute();
		}else{
			$stmt = $pdo->prepare("SELECT name FROM Search WHERE (attribute = 'Hideout' OR attribute = 'Collector' OR attribute = 'Other') AND FiR = 'Yes'");
			$stmt->execute();
		}

		




		



		




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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script>
    $(document).ready(function() {
        // チェックボックスの変更を監視
        $('.checkbox').change(function() {
            // チェックボックスの値を取得
            var checkboxName = $(this).attr('name');
			var checkboxValue =  $(this).is(':checked') ? 1 : 0;

            // Ajaxリクエストを送信
            $.ajax({
                url: 'Quest.php', // PHPスクリプトのパスを指定
                type: 'POST',
                data: { "name":checkboxName, "value":checkboxValue},
                success: function() {
                    // 成功時の処理
					console.log(checkboxName + " " + checkboxValue);
                    console.log("Ajaxリクエストに成功");
                },
                error: function() {
                    // エラー時の処理
                    console.log("Ajaxリクエストに失敗");
                }
            });
        });
    });

	</script>





</head>
<body>

	<div class="container">
		<div class="top">

			<div class="title">
				<a href="top.html">
					<p>Item Management</p>
				</a>
			</div>
			
				<form class="form_quest" action=" ">
					<input type="text" class="input_text" placeholder="Search Item Name"/> 

					<div class="boxes">
						<input type="checkbox" id="box-1" class="checkbox" name="Hideout">
						<label for="box-1">Hideout</label>

						<input type="checkbox" id="box-2" class="checkbox" name="Collector">
						<label for="box-2">Collector</label>

						<input type="checkbox" id="box-3" class="checkbox" name="Other">
						<label for="box-3">Other Task</label>

						<input type="checkbox" id="box-4" class="checkbox" name="FiR">
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
			if(isset($_POST['name']) && isset($_POST['value'])){
				while($row = $stmt->fetch()){
					echo "<div class='panel'>";
						echo "<div class='left'>";
							echo "<p>$row[0]</p>";
							echo "<img src='items/$row[0].png'>";
						echo "</div>";
						echo "<div class='right'>";
							echo "<form method='POST' action=' '>";
								echo "<input type='submit' class='button_minus' value='minus'>";
							echo "</form>";
							echo "<p>0/4</p>";
							echo "<form method='POST' action=' '>";
								echo "<input type='submit' class='button_plus' value='plus'>";
							echo "</form>";
						echo "</div>";
					echo "</div>";
				}
			}

			?>

		</div>

	</div>
	
</body>
</html>

