<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST['email']) && isset($_POST['password'])) {
			$email = $_POST['email'];
			$password = $_POST['password'];
			$email = filter_var($email, FILTER_SANITIZE_EMAIL);
			$password = filter_var($password, FILTER_SANITIZE_STRING);
			$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
			$stmt->execute([$email]);
			$admin = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($admin && password_verify($password, $admin['password'])) {
				$_SESSION['admin'] = true;
			}
		}
	}
?>