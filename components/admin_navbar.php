<?php
$currentPage = basename($_SERVER['PHP_SELF']);

# Helper function to generate navigation links with active state
function adminNavLink(string $href, string $label, string $currentPage): string {
    $isActive = $currentPage === $href;
    $classes = $isActive
        ? 'bg-primary-shadow text-secondary font-semibold'
        : 'hover:bg-primary-shadow hover:text-secondary';
    $ariaCurrent = $isActive ? ' aria-current="page"' : '';

    return sprintf(
        '<a href="%s"%s class="block rounded"><div class="p-2 rounded cursor-pointer transition-colors %s">%s</div></a>',
        htmlspecialchars($href, ENT_QUOTES, 'UTF-8'),
        $ariaCurrent,
        $classes,
        htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
    );
}
?>

<div class="w-64 bg-primary text-primary p-6 fixed min-h-screen flex flex-col justify-between">
    <div>
        <h2 class="text-2xl font-bold mb-6 font-satisfy">J.M. Apilado Resort</h2>
        <div class="space-y-2">
            <?= adminNavLink('admin_dashboard.php', 'Dashboard', $currentPage) ?>
            <?= adminNavLink('booked_client.php', 'Client Booking', $currentPage) ?>
            <?= adminNavLink('reports.php', 'Sales Report', $currentPage) ?>
        </div>
    </div>
    <div class="mt-auto">
        <a href="logout.php" class="block rounded"><div class="p-2 rounded cursor-pointer transition-colors hover:bg-primary-shadow hover:text-secondary">Logout</div></a>
    </div>
</div>
