<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
  <meta name="description" content="{{env('APP_DESCRIPTION', '')}}">
  <meta name="author" content="{{env('APP_AUTHOR', '')}}">
  <meta name="keywords" content="{{env('APP_KEYWORDS', '')}}"

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- index.css here -->
  <link href="{{ asset('css/index.css') }}?@version" rel="stylesheet">

  <!-- favicon -->
  <link rel="icon" type="image/png" href="{{ asset('img/logo/llama_emoji.png') }}?@version">

  <!-- app js values -->
  <script type="application/javascript">
    window.APP_GLOBALS = {};
    window.APP_GLOBALS.APP_URL = '{{ env('APP_URL') }}';
    window.APP_GLOBALS.AUTH_USER = {!! json_encode($auth_user); !!};
    window.APP_GLOBALS.APP_VERSION = '@version()';
  </script>
  <script src="http://alpaca.vagrant/js/manifest.js?HEAD"></script>
  <script src="http://alpaca.vagrant/js/vendor.js?HEAD"></script>
  <script src="http://alpaca.vagrant/js/app.js?HEAD"></script>
</head>
<body>
  <div id="app">
    <router-view />
  </div>

  <!-- Scripts -->
  <script src="{{ asset('js/manifest.js') }}?@version()"></script>
  <script src="{{ asset('js/vendor.js') }}?@version()"></script>
  <script src="{{ asset('js/app.js') }}?@version()"></script>
</body>
</html>
