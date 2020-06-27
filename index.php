<?php

require_once('functions.php');

if(isset($_POST['submit'])) {

				$name = $_POST['name'];
				$name = htmlspecialchars($name, ENT_QUOTES);

				$dbh = db_connect();
// 
				try{
				$dsn = 'mysql:dbname=todolist; host=localhost; charset=utf8';
				$user = 'root';
				$password = '';

				$dbh = new PDO($dsn, $user, $password);
				$dbh->query('SET NAMES utf8');
				$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

				} catch (PDOException $e) {
					print "エラー:" . $e->getMessage() . "<br/>";
					die();
				}

				$sql = 'INSERT INTO tasks (name, done) VALUES (?, 0)';
				$stmt = $dbh->prepare($sql);
				
				$stmt->bindValue(1, $name, PDO::PARAM_STR);

				$stmt->execute();

				$dbh = null;

				unset($name);
}

if(isset($_POST['method']) && ($_POST['method'] ==='put')){

				$id = $_POST["id"];
				$id = htmlspecialchars($id, ENT_QUOTES);
				$id = (int)$id;

				$dbh = db_connect();

				$sql = 'UPDATE tasks SET done = 1 WHERE id = ?';
				$stmt = $dbh->prepare($sql);


				$stmt->bindValue(1, $id, PDO::PARAM_INT);
				$stmt->execute();

				$dbh = null;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ToDo リスト</title>
</head>
<body>
	<h1>ToDoリスト</h1>
	<form action="index.php" method="post">
	<ul>
		<li><span>タスク名</span></li><input type="text" name="name">
		<li><input type="submit" name="submit"></li>
	</ul>
	</form>
	<ul>
	<?php
	$dbh = db_connect();

	$sql = 'SELECT id, name FROM tasks WHERE done = 0 ORDER BY id DESC';
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$dbh = null;

	while($task = $stmt->fetch(PDO::FETCH_ASSOC)){
		print '<li>';
		print $task["name"];

		print '
						<form action="index.php" method="post">
						<input type="hidden" name="method" value="put">
						<input type="hidden" name="id" value="' . $task['id'] .'">
						<button type="submit">済んだ</button>
						</form>
						' ;
		print '</li>';


		// echo "<pre>";
		// var_dump($task);
		// echo "<?pre>";

	}

	?>
	</ul>
</body>
</html>