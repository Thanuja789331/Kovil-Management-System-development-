<!DOCTYPE html>
<html>
<head>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-cover bg-center min-h-screen"
style="background-image:url('/kovilSystem/public/images/bg.jpg')">

<div class="fixed inset-0 bg-black/60 -z-10"></div>

<div class="bg-green-900 text-white px-6 py-4 flex justify-between items-center">

<h1 class="font-bold text-lg">🛕 Kovil System</h1>

<?php if(isset($_SESSION['user'])): ?>
<div>
<?= htmlspecialchars($_SESSION['user']['name']) ?>
<a href="?url=logout" class="ml-3 bg-red-500 px-3 py-1 rounded">Logout</a>
</div>
<?php endif; ?>

</div>

<div class="p-6">