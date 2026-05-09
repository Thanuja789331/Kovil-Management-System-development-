<?php
/**
 * Very small PDF writer for plain text reports.
 * Keeps dependency footprint zero.
 */

function outputSimpleDonationPdf(array $rows, $filename, array $filters = []) {
    $lines = [];
    $lines[] = "Kovil Management System - Donations Done Report";
    $lines[] = "Generated: " . date('Y-m-d H:i:s');
    if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
        $lines[] = "Range: {$filters['start_date']} to {$filters['end_date']}";
    }
    $lines[] = str_repeat('-', 70);

    $total = 0.0;
    foreach ($rows as $index => $row) {
        $amount = (float) ($row['amount'] ?? 0);
        $total += $amount;
        $date = !empty($row['created_at']) ? date('Y-m-d', strtotime($row['created_at'])) : 'N/A';
        $purpose = trim((string) ($row['purpose'] ?? 'General'));
        $lines[] = sprintf(
            "%d. %s | %s | %.2f | %s",
            $index + 1,
            ($row['donor_name'] ?? 'Unknown'),
            $date,
            $amount,
            $purpose === '' ? 'General' : $purpose
        );
    }
    $lines[] = str_repeat('-', 70);
    $lines[] = "Completed Donations: " . count($rows);
    $lines[] = "Total Amount: " . number_format($total, 2);

    $text = implode("\n", $lines);
    $pdf = buildSimplePdfFromText($text);

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
}

function outputSimpleBookingPdf(array $booking, array $bringItems, $filename) {
    $lines = [];
    $lines[] = "Kovil Management System - Pooja Booking Receipt";
    $lines[] = "Generated: " . date('Y-m-d H:i:s');
    $lines[] = str_repeat('-', 70);
    $lines[] = "Booking ID: " . ($booking['id'] ?? 'N/A');
    $lines[] = "Reference: " . ($booking['booking_reference'] ?? 'N/A');
    $lines[] = "Booked By: " . ($booking['user_name'] ?? 'N/A');
    $lines[] = "Email: " . ($booking['email'] ?? 'N/A');
    $lines[] = "Phone: " . (($booking['phone'] ?? '') === '' ? 'N/A' : $booking['phone']);
    $lines[] = "Pooja: " . ($booking['pooja_name'] ?? 'N/A');
    $lines[] = "Date: " . (!empty($booking['pooja_date']) ? date('Y-m-d', strtotime($booking['pooja_date'])) : 'N/A');
    $lines[] = "Time: " . ($booking['time_slot'] ?? 'N/A');
    $lines[] = "Status: " . ucfirst($booking['status'] ?? 'confirmed');
    if (!empty($booking['special_requests'])) {
        $lines[] = "Special Requests: " . trim((string) $booking['special_requests']);
    }
    $lines[] = str_repeat('-', 70);
    $lines[] = "Items To Bring";
    foreach ($bringItems as $index => $item) {
        $lines[] = ($index + 1) . ". " . $item;
    }
    $lines[] = str_repeat('-', 70);
    $lines[] = "Please arrive at least 15 minutes early.";
    $lines[] = "Show this receipt at the temple.";

    $text = implode("\n", $lines);
    $pdf = buildSimplePdfFromText($text);

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
}

function buildSimplePdfFromText($text) {
    $safeText = str_replace(["\\", "(", ")", "\r"], ["\\\\", "\\(", "\\)", ""], $text);
    $textLines = explode("\n", $safeText);

    $y = 790;
    $content = "BT\n/F1 10 Tf\n50 {$y} Td\n";
    foreach ($textLines as $i => $line) {
        if ($i > 0) {
            $content .= "0 -14 Td\n";
        }
        $content .= "(" . $line . ") Tj\n";
    }
    $content .= "ET";

    $objects = [];
    $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
    $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
    $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>";
    $objects[] = "<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream";
    $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";

    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objects as $i => $obj) {
        $offsets[] = strlen($pdf);
        $pdf .= ($i + 1) . " 0 obj\n" . $obj . "\nendobj\n";
    }
    $xrefStart = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";
    for ($i = 1; $i <= count($objects); $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }
    $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xrefStart}\n%%EOF";
    return $pdf;
}
