<div class="max-w-4xl mx-auto">
<h2 class="text-2xl font-bold mb-4 text-white">Announcements</h2>

<?php if($data->num_rows > 0): ?>
<?php while($row=$data->fetch_assoc()): ?>
<div class="bg-white p-4 mb-3 rounded shadow">
<h3 class="font-bold text-lg text-orange-600"><?= htmlspecialchars($row['title']) ?></h3>
<p class="text-gray-700 mt-2"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
<small class="text-gray-500 block mt-2">
<?php if(isset($row['created_at'])): ?>
Posted on: <?= date("F j, Y", strtotime($row['created_at'])) ?>
<?php endif; ?>
</small>
</div>
<?php endwhile; ?>
<?php else: ?>
<p class="text-white text-center">No announcements at this time.</p>
<?php endif; ?>
</div>