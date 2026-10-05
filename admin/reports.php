<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
header('Cache-Control: no-store');
function reportInput(string $key, string $default = ''): string {
    return isset($_GET[$key]) && is_string($_GET[$key]) ? $_GET[$key] : $default;
}
function reportEscape($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function reportMoney($value): string { return '&#8369;' . number_format((float)$value, 2); }
function reportDate(string $value): ?DateTimeImmutable {
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, new DateTimeZone('Asia/Manila'));
    return $date && $date->format('Y-m-d') === $value && $date->format('Y') >= '1000' && $date->format('Y') < '9999' ? $date : null;
}
function reportDisplayDate(?string $value): string {
    if (!$value || strpos($value, '0000-00-00') === 0) return 'Unavailable';
    return reportEscape((new DateTimeImmutable($value))->format('M j, Y'));
}
function reportUrl(array $params): string { return 'reports.php?' . http_build_query($params); }
function reportStatement(mysqli $conn, string $sql, array $params = []): mysqli_stmt {
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new RuntimeException('Unable to prepare report query.');
    if ($params) {
        $types = implode('', array_map(function ($value) { return is_int($value) ? 'i' : 's'; }, $params));
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) throw new RuntimeException('Unable to execute report query.');
    return $stmt;
}
function reportQuery(mysqli $conn, string $sql, array $params = []): array {
    $stmt = reportStatement($conn, $sql, $params);
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}
function reportCsvCell($value): string {
    $value = (string)$value;
    return preg_match('/^[\s\x00-\x1f]*[=+@-]/u', $value) ? "'" . $value : $value;
}
$today = new DateTimeImmutable('today', new DateTimeZone('Asia/Manila'));
$presets = [
    'month' => [$today->modify('first day of this month'), $today->modify('last day of this month')],
    'last-month' => [$today->modify('first day of last month'), $today->modify('last day of last month')],
    'year' => [$today->setDate((int)$today->format('Y'), 1, 1), $today->setDate((int)$today->format('Y'), 12, 31)],
];
$period = reportInput('period', 'month');
if (!isset($presets[$period]) && $period !== 'custom') $period = 'month';
[$start, $end] = $presets[$period === 'custom' ? 'month' : $period];
$error = '';
if ($period === 'custom') {
    $customStart = reportDate(reportInput('start'));
    $customEnd = reportDate(reportInput('end'));
    if (!$customStart || !$customEnd || $customStart > $customEnd) {
        $error = 'Choose valid dates with the start date before or equal to the end date.';
    } else {
        $start = $customStart;
        $end = $customEnd;
    }
}
$packageId = max(0, (int)reportInput('package', '0'));
$search = mb_substr(trim(reportInput('search')), 0, 100);
$page = max(1, (int)reportInput('page', '1'));
$pageSize = 15;
$packages = $breakdown = $transactions = $trend = [];
$summary = ['revenue' => 0, 'paid_count' => 0, 'pending_value' => 0, 'pending_count' => 0];
$totalRows = 0;
$isExport = reportInput('export') === 'csv';
$databaseError = false;
if (!$error) {
    try {
        require_once __DIR__ . '/../conn.php';
        $conn->set_charset('utf8mb4');
        $packages = reportQuery($conn, 'SELECT id, name FROM packages ORDER BY name');
        if ($packageId && !in_array($packageId, array_column($packages, 'id'))) $packageId = 0;
        $from = ' FROM bookings b LEFT JOIN packages p ON p.id = b.package_id
                  LEFT JOIN payment_statuses ps ON ps.id = b.payment_status_id
                  LEFT JOIN statuses s ON s.id = b.status_id';
        $where = ' WHERE b.time_in >= ? AND b.time_in < ?';
        $params = [$start->format('Y-m-d'), $end->modify('+1 day')->format('Y-m-d')];
        if ($packageId) { $where .= ' AND b.package_id = ?'; $params[] = $packageId; }
        $paid = "UPPER(TRIM(ps.name)) = 'PAID'";
        $pending = "UPPER(TRIM(ps.name)) = 'PENDING' AND COALESCE(UPPER(TRIM(s.name)), '') NOT IN ('CANCELLED', 'CANCELED')";
        $summary = reportQuery($conn, "SELECT
            COALESCE(SUM(CASE WHEN $paid THEN p.price ELSE 0 END), 0) AS revenue,
            COALESCE(SUM(CASE WHEN $paid THEN 1 ELSE 0 END), 0) AS paid_count,
            COALESCE(SUM(CASE WHEN $pending THEN p.price ELSE 0 END), 0) AS pending_value,
            COALESCE(SUM(CASE WHEN $pending THEN 1 ELSE 0 END), 0) AS pending_count" . $from . $where, $params)[0];
        $breakdown = reportQuery($conn, "SELECT COALESCE(p.name, 'Unavailable package') AS package_name,
            COUNT(*) AS bookings, COALESCE(SUM(p.price), 0) AS revenue" . $from . $where . " AND $paid
            GROUP BY b.package_id, p.name ORDER BY revenue DESC, package_name", $params);
        $daily = $start->diff($end)->days <= 62;
        $dateFormat = $daily ? '%Y-%m-%d' : '%Y-%m';
        $trendRows = reportQuery($conn, "SELECT DATE_FORMAT(b.time_in, '$dateFormat') AS bucket,
            COALESCE(SUM(p.price), 0) AS revenue" . $from . $where . " AND $paid GROUP BY bucket ORDER BY bucket", $params);
        $values = array_column($trendRows, 'revenue', 'bucket');
        $cursor = $daily ? $start : $start->modify('first day of this month');
        while ($cursor <= $end) {
            $key = $cursor->format($daily ? 'Y-m-d' : 'Y-m');
            $trend[] = ['label' => $cursor->format($daily ? 'M j' : 'M Y'), 'value' => (float)($values[$key] ?? 0)];
            $cursor = $cursor->modify($daily ? '+1 day' : '+1 month');
        }
        $transactionWhere = $where . " AND $paid";
        $transactionParams = $params;
        if ($search !== '') {
            $transactionWhere .= " AND (b.name LIKE ? ESCAPE '=' OR b.email LIKE ? ESCAPE '=' OR b.id LIKE ? ESCAPE '=')";
            $pattern = '%' . strtr($search, ['=' => '==', '%' => '=%', '_' => '=_']) . '%';
            array_push($transactionParams, $pattern, $pattern, $pattern);
        }
        $totalRows = (int)reportQuery($conn, 'SELECT COUNT(*) AS total' . $from . $transactionWhere, $transactionParams)[0]['total'];
        $totalPages = max(1, (int)ceil($totalRows / $pageSize));
        $page = min($page, $totalPages);
        $transactionSql = 'SELECT b.id, b.name, b.email, b.time_in, b.time_out,
            COALESCE(p.name, \'Unavailable package\') AS package_name, p.price AS amount' . $from . $transactionWhere . ' ORDER BY b.time_in DESC, b.id';
        if ($isExport) {
            // Execute before sending download headers so a query failure remains a normal error.
            $stmt = reportStatement($conn, $transactionSql, $transactionParams);
            $result = $stmt->get_result();
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="sales-report-' . $start->format('Y-m-d') . '-to-' . $end->format('Y-m-d') . '.csv"');
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Booking ID', 'Guest', 'Email', 'Package', 'Check-in (Asia/Manila)', 'Check-out (Asia/Manila)', 'Paid package value (PHP)', 'Payment status', 'Report basis']);
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, array_map('reportCsvCell', [$row['id'], $row['name'], $row['email'], $row['package_name'], $row['time_in'], $row['time_out'], $row['amount'] === null ? '' : number_format((float)$row['amount'], 2, '.', ''), 'Paid', 'Current package price; check-in date']));
            }
            fclose($output); $stmt->close(); $conn->close(); exit;
        }
        $transactions = reportQuery($conn, $transactionSql . ' LIMIT ? OFFSET ?', array_merge($transactionParams, [$pageSize, ($page - 1) * $pageSize]));
        $conn->close();
    } catch (Throwable $exception) {
        error_log('Sales report: ' . $exception->getMessage());
        $databaseError = true;
        $error = 'The sales report could not be loaded. Please try again shortly.';
    }
}
if ($isExport) {
    http_response_code($databaseError ? 503 : 400);
    header('Content-Type: text/plain; charset=utf-8');
    echo $error; exit;
}
$filters = ['period' => $period, 'start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d'), 'package' => $packageId];
$rangeLabel = $start->format('M j, Y') . ' – ' . $end->format('M j, Y');
$totalPages = max(1, (int)ceil($totalRows / $pageSize));
$average = $summary['paid_count'] ? (float)$summary['revenue'] / $summary['paid_count'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report · J.M. Apilado Resort</title>
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../tailwind.css"><link rel="stylesheet" href="../css/theme.css"><link rel="stylesheet" href="css/reports.css">
</head>
<body class="sales-report select-none">
    <div class="report-sidebar"><?php include __DIR__ . '/../components/admin_navbar.php'; ?></div>
    <main class="report-main">
        <header class="report-header">
            <div>
                <!-- <p class="eyebrow">Admin / Reports</p> -->
                <h1>Sales report</h1>
                <p class="muted">A clear view of your resort’s booking performance.</p>
            </div>
            <div class="header-actions">
                <!-- <span class="currency-tag">PHP · Philippine peso</span> -->
                <?php if (!$error): ?><a class="button button-dark" href="<?= reportEscape(reportUrl(array_merge($filters, ['search' => $search, 'export' => 'csv']))) ?>">↓ Export CSV</a><?php endif; ?></div>
        </header>
        <form class="report-filters panel" method="get" action="reports.php">
            <label>Period<select name="period" id="report-period"><?php foreach (['month' => 'This month', 'last-month' => 'Last month', 'year' => 'This year', 'custom' => 'Custom dates'] as $value => $label): ?><option value="<?= $value ?>" <?php if (isset($presets[$value])): ?>data-start="<?= $presets[$value][0]->format('Y-m-d') ?>" data-end="<?= $presets[$value][1]->format('Y-m-d') ?>"<?php endif; ?> <?= $period === $value ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></label>
            <label>From<input type="date" name="start" id="report-start" value="<?= reportEscape($start->format('Y-m-d')) ?>" required></label>
            <label>To<input type="date" name="end" id="report-end" value="<?= reportEscape($end->format('Y-m-d')) ?>" required></label>
            <label class="package-filter">Package<select name="package"><option value="0">All packages</option><?php foreach ($packages as $package): ?><option value="<?= (int)$package['id'] ?>" <?= $packageId == $package['id'] ? 'selected' : '' ?>><?= reportEscape($package['name']) ?></option><?php endforeach; ?></select></label>
            <button class="button button-dark" type="submit">Apply filters</button><a class="reset-link" href="reports.php">Reset</a>
        </form>
        <?php if ($error): ?>
            <div class="report-error" role="alert"><strong>Report unavailable</strong><p><?= reportEscape($error) ?></p><a href="reports.php">Reset and try again</a></div>
        <?php else: ?>
        <div class="range-line"><span class="status-dot"></span><strong><?= reportEscape($rangeLabel) ?></strong><span class="muted">Grouped by check-in date</span></div>
        <section class="report-stats" aria-label="Sales summary">
            <article class="stat-card stat-featured"><div class="stat-top"><span>Paid booking value</span><span class="stat-symbol" aria-hidden="true">₱</span></div><p class="stat-value"><?= reportMoney($summary['revenue']) ?></p><p class="stat-note">Total package value of paid bookings</p></article>
            <article class="stat-card"><div class="stat-top"><span>Paid bookings</span><span class="stat-symbol" aria-hidden="true">✓</span></div><p class="stat-value"><?= number_format((int)$summary['paid_count']) ?></p><p class="stat-note">Bookings marked as paid</p></article>
            <article class="stat-card"><div class="stat-top"><span>Average booking value</span><span class="stat-symbol" aria-hidden="true">↗</span></div><p class="stat-value"><?= reportMoney($average) ?></p><p class="stat-note">Per paid booking</p></article>
            <article class="stat-card"><div class="stat-top"><span>Pending booking value</span><span class="stat-symbol pending-symbol" aria-hidden="true">◷</span></div><p class="stat-value"><?= reportMoney($summary['pending_value']) ?></p><p class="stat-note"><?= number_format((int)$summary['pending_count']) ?> unpaid bookings · excludes cancellations</p></article>
        </section>
        <div class="report-charts">
            <section class="panel chart-panel"><div class="section-heading"><div><p class="eyebrow">Performance</p><h2>Paid booking value over time</h2></div><span class="legend"><span class="status-dot"></span>Paid value</span></div>
            <?php if (!$summary['paid_count']): ?><div class="empty-state chart-empty"><span class="empty-icon" aria-hidden="true">↗</span><h3>No paid bookings yet</h3><p>Paid bookings in this period will appear here.<br>Try a different date range to explore past activity.</p></div>
            <?php else: ?>
                <?php $maxValue = max(1, max(array_column($trend, 'value'))); $points = []; foreach ($trend as $i => $point) { $points[] = (count($trend) > 1 ? $i * 800 / (count($trend) - 1) : 400) . ',' . (190 - $point['value'] / $maxValue * 165); } ?>
                <div class="chart-scale"><span><?= reportMoney($maxValue) ?></span><span>Check-in <?= $daily ? 'day' : 'month' ?></span></div>
                <svg class="revenue-chart" viewBox="0 0 800 210" role="img" aria-labelledby="chart-title chart-desc"><title id="chart-title">Paid booking value by <?= $daily ? 'day' : 'month' ?></title><desc id="chart-desc"><?= reportEscape($rangeLabel) ?>. Total <?= reportMoney($summary['revenue']) ?>. Exact values are available in the chart data table.</desc><defs><linearGradient id="chart-fill" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#45b8a2" stop-opacity=".25"/><stop offset="100%" stop-color="#45b8a2" stop-opacity="0"/></linearGradient></defs><?php foreach ([25, 80, 135, 190] as $y): ?><line x1="0" y1="<?= $y ?>" x2="800" y2="<?= $y ?>" stroke="#e5eeec" stroke-dasharray="4 5"/><?php endforeach; ?><polygon points="0,190 <?= implode(' ', $points) ?> 800,190" fill="url(#chart-fill)"/><polyline points="<?= implode(' ', $points) ?>" fill="none" stroke="#168574" stroke-width="3" stroke-linejoin="round"/><?php foreach ($trend as $i => $point): ?><circle cx="<?= count($trend) > 1 ? $i * 800 / (count($trend) - 1) : 400 ?>" cy="<?= 190 - $point['value'] / $maxValue * 165 ?>" r="3" fill="#168574"><title><?= reportEscape($point['label']) ?>: <?= reportMoney($point['value']) ?></title></circle><?php endforeach; ?></svg>
                <div class="chart-labels"><span><?= reportEscape($trend[0]['label']) ?></span><span><?= reportEscape($trend[(int)floor((count($trend) - 1) / 2)]['label']) ?></span><span><?= reportEscape($trend[count($trend) - 1]['label']) ?></span></div>
                <details class="chart-details"><summary>View chart data</summary><div class="chart-data"><table><thead><tr><th scope="col">Period</th><th scope="col">Paid value</th></tr></thead><tbody><?php foreach ($trend as $point): ?><tr><td><?= reportEscape($point['label']) ?></td><td><?= reportMoney($point['value']) ?></td></tr><?php endforeach; ?></tbody></table></div></details>
            <?php endif; ?></section>
            <section class="panel package-panel"><div class="section-heading"><div><p class="eyebrow">Revenue mix</p><h2>Sales by package</h2></div></div><?php if (!$breakdown): ?><div class="empty-state"><span class="empty-icon" aria-hidden="true">◇</span><h3>No package sales</h3><p>Your paid booking mix will appear here.</p></div><?php else: ?><div class="package-list"><?php foreach ($breakdown as $item): $share = $summary['revenue'] > 0 ? (float)$item['revenue'] / $summary['revenue'] * 100 : 0; ?><div class="package-item"><div class="package-row"><strong><?= reportEscape($item['package_name']) ?></strong><span><?= reportMoney($item['revenue']) ?></span></div><div class="package-bar"><span style="width: <?= round($share, 2) ?>%"></span></div><div class="package-meta"><span><?= (int)$item['bookings'] ?> paid bookings</span><span><?= number_format($share, 1) ?>%</span></div></div><?php endforeach; ?></div><?php endif; ?></section>
        </div>
        <section class="panel transaction-panel"><div class="section-heading"><div><p class="eyebrow">Booking records</p><h2>Paid bookings <span class="count-badge"><?= number_format($totalRows) ?></span></h2></div><form class="transaction-search" method="get" action="reports.php"><?php foreach ($filters as $key => $value): ?><input type="hidden" name="<?= reportEscape($key) ?>" value="<?= reportEscape($value) ?>"><?php endforeach; ?><label class="sr-only" for="transaction-search">Search paid bookings by guest, email or booking ID</label><input id="transaction-search" name="search" type="search" placeholder="Guest, email or booking ID" value="<?= reportEscape($search) ?>" maxlength="100"><button class="button button-light" type="submit">Search</button><?php if ($search !== ''): ?><a class="reset-link" href="<?= reportEscape(reportUrl($filters)) ?>">Clear</a><?php endif; ?></form></div>
        <div class="table-scroll"><table class="transactions"><caption class="sr-only">Paid bookings for <?= reportEscape($rangeLabel) ?></caption><thead><tr><th scope="col">Guest / Booking ID</th><th scope="col">Package</th><th scope="col">Check-in</th><th scope="col">Check-out</th><th scope="col">Payment</th><th scope="col" class="amount-cell">Package value</th></tr></thead><tbody><?php foreach ($transactions as $row): ?><tr><td><strong><?= reportEscape($row['name']) ?></strong><span class="guest-email"><?= reportEscape($row['email']) ?></span><span class="booking-id" title="<?= reportEscape($row['id']) ?>"><?= reportEscape($row['id']) ?></span></td><td><?= reportEscape($row['package_name']) ?></td><td class="nowrap"><?= reportDisplayDate($row['time_in']) ?></td><td class="nowrap"><?= reportDisplayDate($row['time_out']) ?></td><td><span class="paid-badge">● Paid</span></td><td class="amount-cell"><strong><?= $row['amount'] === null ? 'Unavailable' : reportMoney($row['amount']) ?></strong></td></tr><?php endforeach; ?><?php if (!$transactions): ?><tr><td colspan="6"><div class="empty-state"><h3><?= $search !== '' ? 'No matching paid bookings' : 'No paid bookings in this period' ?></h3><p><?= $search !== '' ? 'Try another guest name, email or booking ID.' : 'Adjust the filters to view bookings from another period.' ?></p></div></td></tr><?php endif; ?></tbody></table></div>
        <footer class="table-footer"><span><?= $totalRows ? 'Showing ' . (($page - 1) * $pageSize + 1) . '–' . min($page * $pageSize, $totalRows) . ' of ' . number_format($totalRows) : '0 paid bookings' ?><?= $search !== '' ? ' matching your search' : '' ?></span><nav class="pagination" aria-label="Paid booking pages"><?php if ($page > 1): ?><a class="button button-light" rel="prev" href="<?= reportEscape(reportUrl(array_merge($filters, ['search' => $search, 'page' => $page - 1]))) ?>">← Previous</a><?php endif; ?><span>Page <?= $page ?> of <?= $totalPages ?></span><?php if ($page < $totalPages): ?><a class="button button-light" rel="next" href="<?= reportEscape(reportUrl(array_merge($filters, ['search' => $search, 'page' => $page + 1]))) ?>">Next →</a><?php endif; ?></nav></footer></section>
        <!-- <aside class="report-basis"><span aria-hidden="true">ⓘ</span><p><strong>How this report is calculated.</strong> Values use current package prices for bookings marked Paid, grouped by check-in date. They may change when package prices change. Payment amounts, payment dates, methods and refunds are not recorded, so this report reflects booking value rather than cash collected. CSV exports include all paid bookings matching the current filters and search.</p></aside> -->
        <?php endif; ?>
        <!-- <footer class="report-footer"><span>J.M. Apilado Resort</span><span>Sales reporting · Asia/Manila</span></footer> -->
    </main><script>
    // Auto-submit search form after typing stops for 400ms, and restore scroll position after page reload
    (() => {
        const searchForm = document.querySelector('.transaction-search');
        const searchInput = document.getElementById('transaction-search');
        if (!searchForm || !searchInput) return;

        const scrollPositionKey = 'salesReportSearchScrollY';
        let searchTimer;
        searchInput.addEventListener('input', () => {
            window.clearTimeout(searchTimer);
            searchTimer = window.setTimeout(() => searchForm.requestSubmit(), 400);
        });
        searchForm.addEventListener('submit', () => {
            window.clearTimeout(searchTimer);
            sessionStorage.setItem(scrollPositionKey, String(window.scrollY));
        });

        const savedScrollPosition = sessionStorage.getItem(scrollPositionKey);
        if (savedScrollPosition !== null) {
            sessionStorage.removeItem(scrollPositionKey);
            window.addEventListener('pageshow', () => {
                window.requestAnimationFrame(() => window.scrollTo(0, Number(savedScrollPosition)));
            }, { once: true });
        }
    })();

    (() => {
        const period = document.getElementById('report-period');
        const start = document.getElementById('report-start');
        const end = document.getElementById('report-end');
        if (!period || !start || !end) return;
        const syncDates = () => {
            const custom = period.value === 'custom';
            start.readOnly = !custom;
            end.readOnly = !custom;
            end.min = custom ? start.value : '';
        };
        period.addEventListener('change', () => {
            const option = period.selectedOptions[0];
            if (option.dataset.start && option.dataset.end) {
                start.value = option.dataset.start;
                end.value = option.dataset.end;
            }
            syncDates();
        });
        start.addEventListener('change', syncDates);
        syncDates();
    })();
    </script>
</body></html>
