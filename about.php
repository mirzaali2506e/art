<?php
require_once __DIR__ . '/config/functions.php';

$pageTitle = 'About Us';
$activePage = 'about';
include __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb container">
    <a href="index.php">Home</a><span class="sep">/</span>
    <span>About</span>
</div>

<section class="section">
    <div class="container" style="max-width:800px">
        <div class="text-center mb-4">
            <h1>Our Story</h1>
            <div class="line" style="width:60px;height:3px;background:var(--accent-500);margin:1rem auto;border-radius:9999px"></div>
        </div>

        <div class="hero-image mb-4" style="aspect-ratio:16/9;max-height:400px">
            <img src="https://images.pexels.com/photos/18609437/pexels-photo-18609437.jpeg?auto=compress&cs=tinysrgb&h=650&w=940" alt="Our Workshop">
        </div>

        <div style="font-size:1.1rem;line-height:1.8;color:var(--neutral-700)">
            <p class="mb-3">Welcome to <strong>Tooba Art Collection</strong> — where creativity meets quality. We started with a simple passion: to provide crafters, jewelry makers, and DIY enthusiasts with the finest beads, charms, and craft supplies at fair prices.</p>

            <p class="mb-3">What began as a small hobby has grown into a trusted online destination for thousands of makers across Pakistan. Whether you're crafting a single bracelet or stocking up for a creative business, we've got you covered with an ever-growing collection of premium products.</p>

            <h3 class="mt-4 mb-2">Why Choose Us?</h3>
            <ul style="list-style:none;padding:0">
                <li style="padding:0.5rem 0;border-bottom:1px solid var(--neutral-200)"><strong>Quality First</strong> — Every product is hand-checked before shipping.</li>
                <li style="padding:0.5rem 0;border-bottom:1px solid var(--neutral-200)"><strong>Wide Selection</strong> — 500+ products across 30+ collections.</li>
                <li style="padding:0.5rem 0;border-bottom:1px solid var(--neutral-200)"><strong>Fast Delivery</strong> — Orders processed within 1-2 business days.</li>
                <li style="padding:0.5rem 0;border-bottom:1px solid var(--neutral-200)"><strong>Cash on Delivery</strong> — Pay when you receive your order.</li>
                <li style="padding:0.5rem 0"><strong>WhatsApp Support</strong> — Real people, ready to help anytime.</li>
            </ul>

            <h3 class="mt-4 mb-2">Our Promise</h3>
            <p class="mb-3">We believe crafting should be joyful and stress-free. That's why we're committed to honest pricing, accurate product photos, and responsive customer service. If something's not right, we'll make it right — that's our guarantee.</p>
        </div>

        <div class="text-center mt-4">
            <a href="collections.php" class="btn btn-primary btn-lg">Explore Our Collections</a>
            <a href="contact.php" class="btn btn-outline btn-lg">Get in Touch</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
