<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>

	<form method="POST" action="http://localhost:8081/api/upload" enctype="multipart/form-data">

		{{ csrf_field() }}
    <input type="text" id="account_id" name="account_id">

	<input type="file" id="file" name="file" class="form-control">

	<button type="submit">アップロード</button>

	</form>

</body>
</html>