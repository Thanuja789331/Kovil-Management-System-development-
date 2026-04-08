<div class="h-[80vh] flex items-center justify-center">

<form method="POST" class="bg-white p-6 rounded shadow w-96">

<h2 class="text-center font-bold mb-4">Login</h2>

<?php if(isset($error)): ?>
<p class="text-red-500 text-sm"><?= $error ?></p>
<?php endif; ?>

<input name="email" placeholder="Email" class="border p-2 w-full mb-2">
<input name="password" type="password" placeholder="Password" class="border p-2 w-full mb-3">

<button class="bg-green-600 text-white w-full p-2">Login</button>

<p class="text-sm mt-3 text-center">
<a href="?url=register">Register</a>
</p>

</form>
</div>