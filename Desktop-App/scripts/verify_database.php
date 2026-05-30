<?php
/**
 * Database verification script for Kovil Management System.
 * Run: php scripts/verify_database.php
 */
require_once __DIR__ . '/../config/config.php';

$expectedTables = [
    'users',
    'pooja_schedule',
    'bookings',
    'priest_duties',
    'donations',
    'announcements',
    'festivals',
    'special_days',
    'password_resets',
    'registration_logs',
    'notification_logs',
];

$bookingsColumns = [
    'id', 'booking_reference', 'schedule_id', 'user_id', 'devotee_phone',
    'special_requests', 'notification_preference', 'status',
    'sms_sent', 'confirmation_email_sent', 'reminder_10_day_email_sent',
    'reminder_3_day_email_sent', 'created_at',
];

$donationsColumns = [
    'id', 'donor_name', 'amount', 'purpose', 'payment_method',
    'donation_reference', 'payment_status', 'created_at',
];

$bookingForeignKeys = ['fk_bookings_schedule', 'fk_bookings_user'];

$issues = [];
$warnings = [];
$ok = [];

function line(string $level, string $message): void {
    $prefix = match ($level) {
        'OK' => '[OK]   ',
        'WARN' => '[WARN] ',
        'FAIL' => '[FAIL] ',
        default => '[INFO] ',
    };
    echo $prefix . $message . PHP_EOL;
}

echo "Kovil DB verification\n";
echo str_repeat('=', 60) . PHP_EOL;

$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($mysqli->connect_error) {
    line('FAIL', 'Cannot connect to MySQL: ' . $mysqli->connect_error);
    exit(1);
}
$ok[] = 'MySQL connection';
line('OK', 'Connected to MySQL at ' . DB_HOST);

$res = $mysqli->query("SELECT VERSION() AS version");
$version = $res ? ($res->fetch_assoc()['version'] ?? 'unknown') : 'unknown';
line('INFO', 'Server version: ' . $version);

$dbExists = $mysqli->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '" . $mysqli->real_escape_string(DB_NAME) . "'");
if (!$dbExists || $dbExists->num_rows === 0) {
    line('FAIL', 'Database "' . DB_NAME . '" does not exist. Import database_complete.sql first.');
    exit(1);
}
$ok[] = 'Database exists';
line('OK', 'Database "' . DB_NAME . '" exists');

$mysqli->select_db(DB_NAME);

foreach ($expectedTables as $table) {
    $escaped = $mysqli->real_escape_string($table);
    $r = $mysqli->query("SHOW TABLES LIKE '{$escaped}'");
    if (!$r || $r->num_rows === 0) {
        $issues[] = "Missing table: {$table}";
        line('FAIL', "Table missing: {$table}");
    } else {
        line('OK', "Table exists: {$table}");
    }
}

function tableEngine(mysqli $db, string $table): ?string {
    $table = $db->real_escape_string($table);
    $r = $db->query("SHOW TABLE STATUS WHERE Name = '{$table}'");
    if (!$r || $r->num_rows === 0) {
        return null;
    }
    return $r->fetch_assoc()['Engine'] ?? null;
}

function columnNames(mysqli $db, string $table): array {
    $names = [];
    $table = $db->real_escape_string($table);
    $r = $db->query("SHOW COLUMNS FROM `{$table}`");
    if ($r) {
        while ($row = $r->fetch_assoc()) {
            $names[] = $row['Field'];
        }
    }
    return $names;
}

function foreignKeys(mysqli $db, string $table): array {
    $keys = [];
    $dbName = DB_NAME;
    $table = $db->real_escape_string($table);
    $sql = "
        SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = '" . $db->real_escape_string($dbName) . "'
          AND TABLE_NAME = '{$table}'
          AND REFERENCED_TABLE_NAME IS NOT NULL
    ";
    $r = $db->query($sql);
    if ($r) {
        while ($row = $r->fetch_assoc()) {
            $keys[$row['CONSTRAINT_NAME']] = $row;
        }
    }
    return $keys;
}

foreach (['users', 'pooja_schedule', 'bookings', 'donations'] as $table) {
    $engine = tableEngine($mysqli, $table);
    if ($engine === null) {
        continue;
    }
    if (strtoupper($engine) !== 'INNODB') {
        $issues[] = "{$table} uses {$engine}, expected InnoDB";
        line('FAIL', "Table {$table} engine is {$engine} (need InnoDB for foreign keys)");
    } else {
        line('OK', "Table {$table} engine: InnoDB");
    }
}

$donationsTableExists = ($mysqli->query("SHOW TABLES LIKE 'donations'")?->num_rows ?? 0) > 0;

$bookingCols = columnNames($mysqli, 'bookings');
foreach ($bookingsColumns as $col) {
    if (!in_array($col, $bookingCols, true)) {
        $issues[] = "bookings missing column: {$col}";
        line('FAIL', "bookings missing column: {$col}");
    }
}
if (in_array('confirmed_schedule_key', $bookingCols, true)) {
    $warnings[] = 'bookings has confirmed_schedule_key (optional; app does not require it)';
    line('WARN', 'Column confirmed_schedule_key present (optional legacy column)');
}
if (empty(array_diff($bookingsColumns, $bookingCols))) {
    line('OK', 'bookings has all required columns');
}

if ($donationsTableExists) {
    $donationCols = columnNames($mysqli, 'donations');
    foreach ($donationsColumns as $col) {
        if (!in_array($col, $donationCols, true)) {
            $issues[] = "donations missing column: {$col}";
            line('FAIL', "donations missing column: {$col}");
        }
    }
    if (empty(array_diff($donationsColumns, $donationCols))) {
        line('OK', 'donations has all required columns');
    }
}

$bookingFks = foreignKeys($mysqli, 'bookings');
foreach ($bookingForeignKeys as $fkName) {
    if (!isset($bookingFks[$fkName])) {
        $issues[] = "Missing foreign key: {$fkName}";
        line('FAIL', "bookings missing FK: {$fkName}");
    } else {
        $fk = $bookingFks[$fkName];
        line('OK', sprintf(
            '%s: %s -> %s(%s)',
            $fkName,
            $fk['COLUMN_NAME'],
            $fk['REFERENCED_TABLE_NAME'],
            $fk['REFERENCED_COLUMN_NAME']
        ));
    }
}

$orphanSchedules = $mysqli->query("
    SELECT COUNT(*) AS c FROM bookings b
    LEFT JOIN pooja_schedule p ON p.id = b.schedule_id
    WHERE p.id IS NULL
");
if ($orphanSchedules) {
    $count = (int) $orphanSchedules->fetch_assoc()['c'];
    if ($count > 0) {
        $issues[] = "{$count} booking(s) reference missing schedule_id";
        line('FAIL', "{$count} orphan booking(s): schedule_id not in pooja_schedule");
    } else {
        line('OK', 'No orphan bookings (schedule_id)');
    }
}

$orphanUsers = $mysqli->query("
    SELECT COUNT(*) AS c FROM bookings b
    LEFT JOIN users u ON u.id = b.user_id
    WHERE u.id IS NULL
");
if ($orphanUsers) {
    $count = (int) $orphanUsers->fetch_assoc()['c'];
    if ($count > 0) {
        $issues[] = "{$count} booking(s) reference missing user_id";
        line('FAIL', "{$count} orphan booking(s): user_id not in users");
    } else {
        line('OK', 'No orphan bookings (user_id)');
    }
}

$dupConfirmed = $mysqli->query("
    SELECT schedule_id, COUNT(*) AS c
    FROM bookings
    WHERE status = 'confirmed'
    GROUP BY schedule_id
    HAVING c > 1
");
if ($dupConfirmed && $dupConfirmed->num_rows > 0) {
    while ($row = $dupConfirmed->fetch_assoc()) {
        $warnings[] = 'Duplicate confirmed booking for schedule_id ' . $row['schedule_id'];
        line('WARN', 'Multiple confirmed bookings for schedule_id ' . $row['schedule_id']);
    }
} else {
    line('OK', 'At most one confirmed booking per schedule');
}

if ($donationsTableExists) {
    $dupRefs = $mysqli->query("
        SELECT donation_reference, COUNT(*) AS c
        FROM donations
        WHERE donation_reference IS NOT NULL AND donation_reference != ''
        GROUP BY donation_reference
        HAVING c > 1
    ");
    if ($dupRefs && $dupRefs->num_rows > 0) {
        line('FAIL', 'Duplicate donation_reference values found');
        $issues[] = 'Duplicate donation_reference';
    } else {
        line('OK', 'Donation references are unique');
    }

    $badAmounts = $mysqli->query("SELECT COUNT(*) AS c FROM donations WHERE amount <= 0");
    if ($badAmounts) {
        $count = (int) $badAmounts->fetch_assoc()['c'];
        if ($count > 0) {
            $warnings[] = "{$count} donation(s) with amount <= 0";
            line('WARN', "{$count} donation(s) with invalid amount (<= 0)");
        } else {
            line('OK', 'All donation amounts are positive');
        }
    }

    $nullDonRefs = $mysqli->query("SELECT COUNT(*) AS c FROM donations WHERE donation_reference IS NULL OR donation_reference = ''");
    if ($nullDonRefs) {
        $count = (int) $nullDonRefs->fetch_assoc()['c'];
        if ($count > 0) {
            $warnings[] = "{$count} donation(s) without reference";
            line('WARN', "{$count} donation(s) missing donation_reference (app will backfill on use)");
        } else {
            line('OK', 'All donations have a reference');
        }
    }
}

$counts = [];
foreach (['users', 'pooja_schedule', 'bookings', 'donations'] as $t) {
    $exists = $mysqli->query("SHOW TABLES LIKE '{$t}'");
    if ($exists && $exists->num_rows > 0) {
        $r = $mysqli->query("SELECT COUNT(*) AS c FROM `{$t}`");
        $counts[$t] = $r ? ($r->fetch_assoc()['c'] ?? '?') : '?';
    } else {
        $counts[$t] = 'n/a';
    }
}
line('INFO', 'Row counts: users=' . $counts['users']
    . ', pooja_schedule=' . $counts['pooja_schedule']
    . ', bookings=' . $counts['bookings']
    . ', donations=' . $counts['donations']);

echo str_repeat('=', 60) . PHP_EOL;
if (empty($issues)) {
    line('OK', 'Database check PASSED' . (empty($warnings) ? '' : ' (with warnings)'));
    exit(empty($warnings) ? 0 : 0);
}
line('FAIL', 'Database check FAILED — ' . count($issues) . ' issue(s)');
foreach ($issues as $issue) {
    echo '       - ' . $issue . PHP_EOL;
}
if (!empty($warnings)) {
    echo 'Warnings:' . PHP_EOL;
    foreach ($warnings as $warning) {
        echo '       - ' . $warning . PHP_EOL;
    }
}
exit(1);
