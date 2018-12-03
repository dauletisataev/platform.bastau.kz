<!DOCTYPE html>
<html lang="ru" xmlns:v-bind="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <title>Загрузка...</title>
    <link rel="stylesheet" href="{{ asset("/css/main.css?".random_int(1000, 9999)) }}">
    <link rel="stylesheet" href="{{ asset("/css/app.css") }}">
    <link rel="stylesheet" href="{{ asset("/css/quill.core.css") }}">
    <link rel="stylesheet" href="{{ asset("/css/quill.bubble.css") }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="client-secret" content="CUtVkTnoI3GSQ4wFm3kgIyh7ER8TDO8lFOzfBj50">
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