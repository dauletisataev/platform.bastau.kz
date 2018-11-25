<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка...</title>
    <link rel="stylesheet" href="{{ asset("/css/main.css?".random_int(1000, 9999)) }}">
    <link rel="stylesheet" href="{{ asset("/css/app.css") }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
<<<<<<< HEAD
    <meta name="client-secret" content="k8oWZceXbub1Fg8ymQBQBBh53sXXwjNXyuXQnLqk">
=======
    <meta name="client-secret" content="JveOxSb9hlu4TK70Ijc1i7sjrHfVOL0Tu3RPsPG7">
>>>>>>> remotes/origin/master

</head>
<body>
<div id="app">
    <div v-bind:class="{ 'bg-faded': !user }">

        <sidebar v-if="user"></sidebar>
        <transition name="fade" mode="out-in">
            <router-view></router-view>
        </transition>

    </div>
</div>



<script>
    window.Laravel = <?php echo json_encode([
        'csrfToken' => csrf_token(),
    ]); ?>
</script>
<script src="{{ asset("/js/app.js?v.1.7") }}" charset="utf-8"></script>


</body>
</html>
