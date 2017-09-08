<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Yaf_Demo</title>
    <!-- Styles -->
    <style>

    </style>
</head>
<body style="padding:15px;">
<form action="<?php echo BASE_URL;?>User/userSave" method="post" enctype="multipart/form-data">
    <input type="file" name="upfile" /><br>
    nickname: <input type="text" name="nickname" value="<?php echo $user['nickname'];?>"><br>
    sex: <input type="text" name="sex" value="<?php echo $user['sex'];?>"><br>
    birthday: <input type="text" name="birthday" value="<?php echo $user['birthday'];?>"><br>
    province: <input type="text" name="province" value="<?php echo $user['province'];?>"><br>
    city: <input type="text" name="city" value="<?php echo $user['city'];?>"><br>
    area: <input type="text" name="area" value="<?php echo $user['area'];?>"><br>
    id: <input type="text" name="id" value="<?php echo $user['id'];?>"><br>
    <input type="submit">
</form>
</body>
</html>
