<?php while($row = $data->fetch_assoc()): ?>
<div class="bg-white p-3 mb-2 flex justify-between">

<div>
<?= htmlspecialchars($row['pooja_name']) ?><br>
<span class="text-sm"><?= $row['time_slot'] ?></span>
</div>

<a href="?url=book&id=<?= $row['id'] ?>"
class="bg-green-600 text-white px-3 py-1 rounded">
Book
</a>

</div>
<?php endwhile; ?>