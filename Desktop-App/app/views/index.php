<div class="max-w-4xl mx-auto">
<h2 class="text-2xl font-bold mb-4 text-white">Make a Donation</h2>

<form method="POST" class="bg-white p-6 rounded shadow mb-6">
<input name="name" placeholder="Your Name" class="border p-2 w-full mb-2" required>
<input name="amount" type="number" placeholder="Amount (Rs)" class="border p-2 w-full mb-2" required>
<input name="purpose" placeholder="Purpose (e.g., Temple Maintenance)" class="border p-2 w-full mb-3" required>
<button class="bg-green-600 text-white w-full p-2">Donate Now</button>
</form>

<h2 class="text-2xl font-bold mb-4 text-white">Recent Donations</h2>
<div class="bg-white p-4 rounded shadow">
<?php while($row=$data->fetch_assoc()): ?>
<div class="border-b py-2">
<strong><?= htmlspecialchars($row['donor_name']) ?></strong> - Rs <?= number_format($row['amount'], 2) ?>
<br><small class="text-gray-600"><?= htmlspecialchars($row['purpose']) ?></small>
</div>
<?php endwhile; ?>
</div>
</div>