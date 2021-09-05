<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- We'll check where the user has logged if not than will ask him for login. -->
    @if( !$is_authenticated )
        <a href="{{ $drive_login_url }}">Login to Drive</a>
    @endif

</body>
</html>
