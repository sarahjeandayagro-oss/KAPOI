<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>OPAC — Panabo City Library</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    />

    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">

    <style>

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        :root {

            --primary: #2F9E8F;
            --primary-dark: #26867A;
            --btn-signin-blue: #2F9E8F;

            --primary-light: rgba(47, 158, 143, 0.08);

            --bg: #F5F7FA;
            --panel: #FFFFFF;
            --card: #FFFFFF;

            --border: #E5E7EB;

            --text: #1F2937;
            --muted: #6B7280;

            --success: #22C55E;
            --warning: #F59E0B;
            --error: #EF4444;

            --shadow:
                0 10px 30px rgba(15,23,42,.08);

            --shadow-lg:
                0 20px 60px rgba(15,23,42,.12);

            --radius: 12px;
        }


        /* =====================================================
           BODY / BACKGROUND IMAGE
           ===================================================== */

        body {

            font-family: 'Inter', sans-serif;

            /*
             * LIBRARY BACKGROUND IMAGE
             *
             * Make sure the image is inside:
             *
             * public/images/library-group-study-stockcake.jpg
             */

            background:

                linear-gradient(
                    180deg,
                    rgba(245,247,250,0.72) 0%,
                    rgba(245,247,250,0.82) 100%
                ),

                url("<?php echo e(asset('images/library-group-study-stockcake.jpg')); ?>")
                center center / cover no-repeat fixed;

            color: var(--text);

            min-height: 100vh;

            font-size: 14px;

        }


        /* =====================================================
           HEADER
           ===================================================== */

        .opac-header {

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );

            color: white;

            padding: 16px 32px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            flex-wrap: wrap;

            gap: 12px;

            box-shadow:
                0 4px 20px rgba(47,158,143,.25);
        }


        .header-left {

            display: flex;

            align-items: center;

            gap: 14px;
        }


        .header-logo {

            width: 44px;
            height: 44px;

            border-radius: 10px;

            object-fit: cover;

            border:
                2px solid rgba(255,255,255,.3);
        }


        .header-brand {

            display: flex;

            flex-direction: column;
        }


        .header-brand h1 {

            font-size: 17px;

            font-weight: 700;

            letter-spacing: -0.3px;
        }


        .header-brand span {

            font-size: 12px;

            opacity: 0.85;
        }


        .header-links {

            display: flex;

            gap: 10px;

            flex-wrap: wrap;
        }


        .header-links a {

            color: white;

            text-decoration: none;

            padding: 8px 16px;

            border-radius: 8px;

            font-size: 13px;

            font-weight: 500;

            border:
                1px solid rgba(255,255,255,.3);

            transition: all .2s;
        }


        .header-links a:hover {

            background:
                rgba(255,255,255,.15);

            border-color:
                rgba(255,255,255,.5);
        }


        .header-links a.primary-link {

            background: white;

            color: var(--primary);

            border-color: white;

            font-weight: 600;
        }


        /* =====================================================
           HERO SEARCH
           ===================================================== */

        .hero {

            text-align: center;

            padding: 48px 24px 36px;

        }


        .hero-title {

            font-size: 28px;

            font-weight: 700;

            color: var(--text);

            margin-bottom: 6px;

        }


        .hero-desc {

            color: var(--muted);

            max-width: 680px;

            margin: 0 auto 28px;

            font-size: 14px;

            line-height: 1.7;

        }


        .search-wrapper {

            max-width: 700px;

            margin: 0 auto;

            position: relative;
        }


        .search-box {

            display: flex;

            align-items: center;

            background: rgba(255,255,255,0.96);

            border:
                2px solid var(--border);

            border-radius: 50px;

            padding: 4px 4px 4px 22px;

            box-shadow: var(--shadow-lg);

            transition:
                border-color .2s,
                box-shadow .2s;
        }


        .search-box:focus-within {

            border-color: var(--primary);

            box-shadow:
                0 0 0 4px rgba(47,158,143,.12),
                var(--shadow-lg);
        }


        .search-icon {

            color: var(--muted);

            font-size: 20px;

            flex-shrink: 0;
        }


        .search-input {

            flex: 1;

            border: none;

            outline: none;

            font-size: 15px;

            padding: 14px 16px;

            font-family: 'Inter', sans-serif;

            color: var(--text);

            background: transparent;
        }


        .search-input::placeholder {

            color: var(--muted);
        }


        .search-btn {

            background: var(--primary);

            color: white;

            border: none;

            padding: 12px 28px;

            border-radius: 50px;

            font-size: 14px;

            font-weight: 600;

            font-family: 'Inter', sans-serif;

            cursor: pointer;

            transition: background .2s;

            white-space: nowrap;
        }


        .search-btn:hover {

            background: var(--primary-dark);
        }


        /* =====================================================
           FILTERS
           ===================================================== */

        .filters-bar {

            max-width: 1200px;

            margin: 0 auto 8px;

            padding: 0 24px;

            display: flex;

            gap: 10px;

            flex-wrap: wrap;

            align-items: center;
        }


        .filter-select {

            padding: 8px 14px;

            border:
                1px solid var(--border);

            border-radius: 8px;

            background:
                rgba(255,255,255,0.96);

            font-family: 'Inter', sans-serif;

            font-size: 13px;

            color: var(--text);

            cursor: pointer;

            outline: none;

            transition: border-color .2s;
        }


        .filter-select:focus {

            border-color: var(--primary);
        }


        .active-filters {

            display: flex;

            gap: 6px;

            flex-wrap: wrap;
        }


        .filter-pill {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding: 5px 12px;

            background: var(--primary-light);

            color: var(--primary);

            border-radius: 20px;

            font-size: 12px;

            font-weight: 500;

            cursor: pointer;

            border: none;

            font-family: 'Inter', sans-serif;
        }


        .filter-pill:hover {

            background:
                rgba(47,158,143,.18);
        }


        .results-count {

            margin-left: auto;

            font-size: 13px;

            color: var(--muted);

            white-space: nowrap;
        }


        /* =====================================================
           RESULTS GRID
           ===================================================== */

        .results-container {

            max-width: 1200px;

            margin: 0 auto;

            padding: 12px 24px 60px;

            display: grid;

            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(320px, 1fr)
                );

            gap: 20px;
        }


        /* =====================================================
           BOOK CARD
           ===================================================== */

        .book-card {

            background:
                rgba(255,255,255,0.96);

            border:
                1px solid var(--border);

            border-radius: var(--radius);

            overflow: hidden;

            display: flex;

            flex-direction: column;

            transition:
                transform .2s,
                box-shadow .2s;

            box-shadow: var(--shadow);
        }


        .book-card:hover {

            transform: translateY(-3px);

            box-shadow: var(--shadow-lg);
        }


        .book-card-link {

            display: block;

            color: inherit;

            text-decoration: none;
        }


        .book-card-link:hover .book-card {

            transform: translateY(-3px);
        }


        .card-cover {

            height: 180px;

            display: flex;

            align-items: center;

            justify-content: center;

            position: relative;

            overflow: hidden;
        }


        .card-cover img {

            width: 100%;

            height: 100%;

            object-fit: cover;
        }


        .card-cover-fallback {

            width: 100%;

            height: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 64px;

            font-weight: 700;

            color: white;

            text-transform: uppercase;
        }


        .card-availability {

            position: absolute;

            top: 12px;

            right: 12px;

            padding: 5px 10px;

            border-radius: 6px;

            font-size: 11px;

            font-weight: 600;

            text-transform: uppercase;

            letter-spacing: 0.5px;
        }


        .chip-available {

            background: var(--success);

            color: white;
        }


        .chip-borrowed {

            background: var(--error);

            color: white;
        }


        .card-body {

            padding: 18px;

            flex: 1;

            display: flex;

            flex-direction: column;

            gap: 10px;
        }


        .card-title {

            font-size: 15px;

            font-weight: 700;

            color: var(--text);

            line-height: 1.4;

            display: -webkit-box;

            -webkit-line-clamp: 2;

            -webkit-box-orient: vertical;

            overflow: hidden;
        }


        .card-author {

            font-size: 13px;

            color: var(--muted);
        }


        .card-meta {

            display: flex;

            gap: 8px;

            flex-wrap: wrap;
        }


        .meta-pill {

            padding: 4px 10px;

            border-radius: 6px;

            font-size: 11px;

            font-weight: 500;

            background: var(--bg);

            color: var(--muted);
        }


        .card-details {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 8px;

            font-size: 12px;

            color: var(--muted);

            margin-top: auto;

            padding-top: 12px;

            border-top:
                1px solid var(--border);
        }


        .detail-label {

            font-weight: 500;

            color: var(--text);

            display: block;
        }


        .card-summary {

            font-size: 12px;

            color: var(--muted);

            line-height: 1.5;

            display: -webkit-box;

            -webkit-line-clamp: 2;

            -webkit-box-orient: vertical;

            overflow: hidden;
        }


        /* =====================================================
           EMPTY STATE
           ===================================================== */

        .empty-state {

            grid-column: 1 / -1;

            text-align: center;

            padding: 60px 20px;
        }


        .empty-icon {

            font-size: 64px;

            margin-bottom: 16px;

            opacity: 0.6;
        }


        .empty-title {

            font-size: 18px;

            font-weight: 700;

            color: var(--text);

            margin-bottom: 6px;
        }


        .empty-text {

            color: var(--muted);

            font-size: 14px;
        }


        /* =====================================================
           LOADING
           ===================================================== */

        .loading-spinner {

            grid-column: 1 / -1;

            text-align: center;

            padding: 40px;
        }


        .spinner {

            width: 36px;

            height: 36px;

            border:
                3px solid var(--border);

            border-top-color:
                var(--primary);

            border-radius: 50%;

            animation:
                spin .6s linear infinite;

            margin: 0 auto;
        }


        @keyframes spin {

            to {
                transform: rotate(360deg);
            }

        }


        /* =====================================================
           FOOTER
           ===================================================== */

        .opac-footer {

            text-align: center;

            padding: 20px;

            border-top:
                1px solid var(--border);

            color: var(--muted);

            font-size: 12px;

            margin-top: 20px;

            background:
                rgba(255,255,255,0.90);
        }


        /* =====================================================
           RESPONSIVE
           ===================================================== */

        @media (max-width: 768px) {

            .opac-header {

                padding: 14px 16px;
            }


            .hero {

                padding: 32px 16px 24px;
            }


            .hero-title {

                font-size: 22px;
            }


            .search-box {

                border-radius: 16px;

                flex-direction: column;

                padding: 8px;

                gap: 8px;
            }


            .search-input {

                width: 100%;

                text-align: center;
            }


            .search-btn {

                width: 100%;

                border-radius: 12px;

                padding: 12px;
            }


            .results-container {

                grid-template-columns: 1fr;

                padding:
                    12px 16px 40px;
            }


            .filters-bar {

                padding: 0 16px;
            }


            .header-links {

                font-size: 11px;

                gap: 6px;
            }


            .header-links a {

                padding: 6px 10px;

                font-size: 11px;
            }

        }


        @media (max-width: 480px) {

            .hero-title {

                font-size: 19px;
            }


            .hero-desc {

                font-size: 12px;
            }


            .card-cover {

                height: 140px;
            }

        }

    </style>

</head>


<body>


    <!-- =====================================================
         HEADER
         ===================================================== -->

    <header class="opac-header">

        <div class="header-left">

            <img
                src="<?php echo e(asset('images/library logos.jpg')); ?>"
                class="header-logo"
                alt="Library Logo"
            >

            <div class="header-brand">

                <h1>
                    Panabo City Library
                </h1>

                <span>
                    Online Public Access Catalog
                </span>

            </div>

        </div>


        <div class="header-links">

            <?php if(auth()->guard()->check()): ?>

                <?php

                    $dashRoute = match(auth()->user()->role) {

                        'admin' =>
                            route('admin.dashboard'),

                        'staff' =>
                            route('staff.dashboard'),

                        'researcher' =>
                            route('researcher.dashboard'),

                        'visitor' =>
                            route('visitor.dashboard'),

                        default =>
                            route('student.dashboard'),

                    };


                    $dashLabel = match(auth()->user()->role) {

                        'admin' =>
                            'Admin Panel',

                        'staff' =>
                            'Staff Portal',

                        'researcher' =>
                            'My Research',

                        'visitor' =>
                            'Visitor Portal',

                        default =>
                            'My Dashboard',

                    };

                ?>


                <a href="<?php echo e($dashRoute); ?>">
                    <?php echo e($dashLabel); ?>

                </a>


                <form
                    id="opac-logout"
                    action="<?php echo e(route('logout')); ?>"
                    method="POST"
                    style="display:inline;"
                >

                    <?php echo csrf_field(); ?>

                    <a
                        href="#"
                        onclick="
                            if(window.showConfirm){window.showConfirm('Are you sure you want to log out?').then(function(ok){if(ok)document.getElementById('opac-logout').submit()})}else{if(confirm('Are you sure you want to log out?'))document.getElementById('opac-logout').submit()}
                            return false;
                        "
                        style="
                            background: rgba(255,255,255,0.15);
                        "
                    >
                        Logout
                    </a>

                </form>


            <?php else: ?>

                <a
                    href="<?php echo e(route('login')); ?>"
                    class="primary-link"
                >
                    Login
                </a>

            <?php endif; ?>

        </div>

    </header>



    <!-- =====================================================
         HERO SEARCH
         ===================================================== -->

    <section class="hero">

        <h2 class="hero-title">
            Search the Library Catalog
        </h2>


        <p class="hero-desc">

            Online Public Access Catalog (OPAC) is a digital search tool
            that allows library users to easily find and locate materials
            within the library's collection. Through OPAC, users can search
            for books, theses, journals, and other resources by title,
            author, subject, or keyword.

        </p>


        <div class="search-wrapper">

            <div class="search-box">

                <span class="search-icon">
                    🔍
                </span>


                <input
                    type="text"
                    class="search-input"
                    id="searchInput"
                    placeholder="Search by title, author, subject, or keyword..."
                    autofocus
                >


                <button
                    class="search-btn"
                    id="searchBtn"
                >
                    Search Catalog
                </button>

            </div>

        </div>

    </section>



    <!-- =====================================================
         FILTERS
         ===================================================== -->

    <div class="filters-bar">

        <select
            class="filter-select"
            id="filterType"
        >

            <option value="">
                All Material Types
            </option>

            <option value="Math (Mathematics)">
                Math (Mathematics)
            </option>

            <option value="Science (Science)">
                Science (Science)
            </option>

            <option value="History (Historical)">
                History (Historical)
            </option>

            <option value="Lit (Literature)">
                Lit (Literature)
            </option>

            <option value="Fil (Filipiniana)">
                Filipiniana
            </option>

            <option value="Cir (Circulation)">
                Circulation
            </option>

            <option value="Gen. Ref.">
                General References
            </option>

            <option value="F (Fiction)">
                Fiction
            </option>

            <option value="D (Dissertation)">
                Dissertation
            </option>

            <option value="T (Thesis)">
                Thesis
            </option>

            <option value="Journal">
                Journal
            </option>

        </select>


        <div
            class="filter-select"
            id="filterAvailability"
        >
        </div>


        <div
            class="active-filters"
            id="activeFilters"
        >
        </div>


        <div
            class="results-count"
            id="resultsCount"
        >
            <?php echo e($totalBooks); ?> materials in catalog
        </div>

    </div>



    <!-- =====================================================
         RESULTS CONTAINER
         ===================================================== -->

    <div
        class="results-container"
        id="resultsContainer"
    >

        <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

            <a
                class="book-card-link"
                href="<?php echo e(route('opac.book', ['id' => $book->id])); ?>"
            >

                <div
                    class="book-card"
                    data-id="<?php echo e($book->id); ?>"
                >

                    <div class="card-cover">


                        <?php if($book->cover_image): ?>

                            <img
                                src="<?php echo e('/storage/' . ltrim($book->cover_image, '/')); ?>"
                                alt="<?php echo e($book->title); ?>"
                                loading="lazy"
                            >

                        <?php else: ?>

                            <?php

                                $catColors = [

                                    'Math (Mathematics)' =>
                                        ['#6366F1','#4F46E5'],

                                    'Science (Science)' =>
                                        ['#06B6D4','#0891B2'],

                                    'History (Historical)' =>
                                        ['#A16207','#713F12'],

                                    'Lit (Literature)' =>
                                        ['#D946EF','#B603BD'],

                                    'Fil (Filipiniana)' =>
                                        ['#2F9E8F','#1a7a6e'],

                                    'Cir (Circulation)' =>
                                        ['#3B82F6','#2563EB'],

                                    'Gen. Ref.' =>
                                        ['#8B5CF6','#7C3AED'],

                                    'F (Fiction)' =>
                                        ['#F59E0B','#D97706'],

                                    'D (Dissertation)' =>
                                        ['#EF4444','#DC2626'],

                                    'T (Thesis)' =>
                                        ['#10B981','#059669'],

                                    'Journal' =>
                                        ['#14B8A6','#0F766E'],

                                ];


                                $colors =
                                    $catColors[$book->category]
                                    ?? ['#6B7280','#4B5563'];

                            ?>


                            <div
                                class="card-cover-fallback"
                                style="
                                    background:
                                    linear-gradient(
                                        135deg,
                                        <?php echo e($colors[0]); ?>,
                                        <?php echo e($colors[1]); ?>

                                    );
                                "
                            >

                                <?php echo e(strtoupper(substr($book->title, 0, 1))); ?>


                            </div>

                        <?php endif; ?>


                        <span
                            class="card-availability
                            <?php echo e(($book->available ?? 0) > 0
                                ? 'chip-available'
                                : 'chip-borrowed'); ?>"
                        >

                            <?php echo e(($book->available ?? 0) > 0
                                    ? 'Available'
                                    : 'Unavailable'); ?>


                        </span>

                    </div>



                    <div class="card-body">


                        <div class="card-title">

                            <?php echo e($book->title); ?>


                        </div>


                        <div class="card-author">

                            by <?php echo e($book->author); ?>


                        </div>


                        <div class="card-meta">

                            <span class="meta-pill">

                                <?php echo e($book->category); ?>


                            </span>


                            <?php if(!empty($book->call_number)): ?>

                                <span class="meta-pill">

                                    <?php echo e($book->call_number); ?>


                                </span>

                            <?php endif; ?>

                        </div>


                        <?php if(!empty($book->summary)): ?>

                            <div class="card-summary">

                                <?php echo e($book->summary); ?>


                            </div>

                        <?php endif; ?>


                        <div class="card-details">


                            <div>

                                <span class="detail-label">
                                    Accession ID
                                </span>

                                <?php echo e($book->accession_number ?? 'N/A'); ?>


                            </div>


                            <div>

                                <span class="detail-label">
                                    Copies
                                </span>

                                <?php echo e($book->available ?? 0); ?>/<?php echo e($book->copies ?? 0); ?>


                            </div>


                            <div
                                style="
                                    display: flex;
                                    flex-direction: column;
                                    gap: 6px;
                                "
                            >

                                <span class="detail-label">
                                    Barcode
                                </span>


                                <img
                                    src="<?php echo e(route('barcode.book.png', ['barcode' => $book->barcode])); ?>"
                                    alt="Barcode: <?php echo e($book->barcode); ?>"
                                    style="
                                        height: 40px;
                                        width: 100%;
                                        object-fit: contain;
                                    "
                                >


                                <small
                                    style="
                                        color: var(--muted);
                                        font-family: monospace;
                                        font-size: 11px;
                                    "
                                >
                                    <?php echo e($book->barcode); ?>

                                </small>

                            </div>


                            <div>

                                <span class="detail-label">
                                    Location
                                </span>

                                <?php echo e($book->section_location ?? 'General'); ?>


                            </div>


                        </div>

                    </div>

                </div>

            </a>


        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

            <div class="empty-state">

                <div class="empty-icon">
                    📚
                </div>


                <div class="empty-title">
                    No materials found
                </div>


                <div class="empty-text">

                    The library catalog is currently empty.
                    Please check back later.

                </div>

            </div>

        <?php endif; ?>

    </div>



    <!-- =====================================================
         FOOTER
         ===================================================== -->

    <footer class="opac-footer">

        Panabo City Library &copy; <?php echo e(date('Y')); ?>

        — Online Public Access Catalog

    </footer>



    <!-- =====================================================
         SEARCH SCRIPT
         ===================================================== -->

    <script>

        const searchInput =
            document.getElementById('searchInput');

        const searchBtn =
            document.getElementById('searchBtn');

        const filterType =
            document.getElementById('filterType');

        const filterAvailability =
            document.getElementById('filterAvailability');

        const resultsContainer =
            document.getElementById('resultsContainer');

        const resultsCount =
            document.getElementById('resultsCount');

        let debounceTimer;



        /* =====================================================
           CATEGORY FALLBACK COLORS
           ===================================================== */

        const catColors = {

            'Math (Mathematics)':
                ['#6366F1','#4F46E5'],

            'Science (Science)':
                ['#06B6D4','#0891B2'],

            'History (Historical)':
                ['#A16207','#713F12'],

            'Lit (Literature)':
                ['#D946EF','#B603BD'],

            'Fil (Filipiniana)':
                ['#2F9E8F','#1a7a6e'],

            'Cir (Circulation)':
                ['#3B82F6','#2563EB'],

            'Gen. Ref.':
                ['#8B5CF6','#7C3AED'],

            'F (Fiction)':
                ['#F59E0B','#D97706'],

            'D (Dissertation)':
                ['#EF4444','#DC2626'],

            'T (Thesis)':
                ['#10B981','#059669'],

            'Journal':
                ['#14B8A6','#0F766E']

        };



        /* =====================================================
           SEARCH
           ===================================================== */

        function doSearch() {


            const params =
                new URLSearchParams();


            const q =
                searchInput.value.trim();


            if (q) {

                params.set('q', q);

            }


            if (filterType.value) {

                params.set(
                    'type',
                    filterType.value
                );

            }


            if (filterAvailability.value) {

                params.set(
                    'availability',
                    filterAvailability.value
                );

            }


            /* SHOW LOADING */

            resultsContainer.innerHTML = `

                <div class="loading-spinner">

                    <div class="spinner"></div>

                </div>

            `;


            /* FETCH SEARCH RESULTS */

            fetch(
                '/opac/search?' +
                params.toString()
            )

            .then(r => r.json())

            .then(data => {


                resultsCount.textContent =
                    data.total +
                    ' material' +
                    (data.total !== 1 ? 's' : '') +
                    ' found';


                if (data.results.length === 0) {


                    resultsContainer.innerHTML = `

                        <div class="empty-state">

                            <div class="empty-icon">
                                🔍
                            </div>

                            <div class="empty-title">
                                No results found
                            </div>

                            <div class="empty-text">
                                Try adjusting your search terms or filters.
                            </div>

                        </div>

                    `;


                    return;

                }


                resultsContainer.innerHTML =
                    data.results
                        .map(book => renderBookCard(book))
                        .join('');

            })


            .catch(err => {


                console.error(err);


                resultsContainer.innerHTML = `

                    <div class="empty-state">

                        <div class="empty-title">
                            Search error
                        </div>

                        <div class="empty-text">
                            Please try again.
                        </div>

                    </div>

                `;

            });

        }



        /* =====================================================
           RENDER BOOK CARD
           ===================================================== */

        function renderBookCard(book) {


            const available =
                (book.available || 0) > 0;


            let coverHtml;


            if (book.cover_url) {


                coverHtml = `

                    <img
                        src="${book.cover_url}"
                        alt="${escapeHtml(book.title)}"
                        loading="lazy"
                    >

                `;

            }

            else {


                const colors =
                    catColors[book.category]
                    || ['#6B7280','#4B5563'];


                coverHtml = `

                    <div
                        class="card-cover-fallback"
                        style="
                            background:
                            linear-gradient(
                                135deg,
                                ${colors[0]},
                                ${colors[1]}
                            );
                        "
                    >

                        ${
                            book.title
                                ? book.title.charAt(0).toUpperCase()
                                : '?'
                        }

                    </div>

                `;

            }


            return `

                <a
                    class="book-card-link"
                    href="/opac/books/${book.id}"
                >

                    <div class="book-card">


                        <div class="card-cover">

                            ${coverHtml}


                            <span
                                class="card-availability
                                ${
                                    available
                                        ? 'chip-available'
                                        : 'chip-borrowed'
                                }"
                            >

                                ${
                                    available
                                        ? 'Available'
                                        : 'Unavailable'
                                }

                            </span>

                        </div>



                        <div class="card-body">


                            <div class="card-title">

                                ${escapeHtml(
                                    book.title || 'Untitled'
                                )}

                            </div>


                            <div class="card-author">

                                by ${
                                    escapeHtml(
                                        book.author || 'Unknown'
                                    )
                                }

                            </div>


                            <div class="card-meta">

                                <span class="meta-pill">

                                    ${
                                        escapeHtml(
                                            book.category ||
                                            'General'
                                        )
                                    }

                                </span>


                                ${
                                    book.call_number
                                        ? `
                                            <span class="meta-pill">

                                                ${
                                                    escapeHtml(
                                                        book.call_number
                                                    )
                                                }

                                            </span>
                                          `
                                        : ''
                                }

                            </div>


                            ${
                                book.summary
                                    ? `
                                        <div class="card-summary">

                                            ${
                                                escapeHtml(
                                                    book.summary
                                                )
                                            }

                                        </div>
                                      `
                                    : ''
                            }


                            <div class="card-details">


                                <div>

                                    <span class="detail-label">
                                        Accession ID
                                    </span>

                                    ${
                                        escapeHtml(
                                            book.accession_number ||
                                            'N/A'
                                        )
                                    }

                                </div>


                                <div>

                                    <span class="detail-label">
                                        Copies
                                    </span>

                                    ${
                                        book.available || 0
                                    }/${
                                        book.copies || 0
                                    }

                                </div>


                                <div
                                    style="
                                        display: flex;
                                        flex-direction: column;
                                        gap: 6px;
                                    "
                                >

                                    <span class="detail-label">
                                        Barcode
                                    </span>


                                    <img
                                        src="/barcode/book/${
                                            escapeHtml(
                                                book.barcode || ''
                                            )
                                        }"
                                        alt="Barcode: ${
                                            escapeHtml(
                                                book.barcode || ''
                                            )
                                        }"
                                        style="
                                            height: 40px;
                                            width: 100%;
                                            object-fit: contain;
                                        "
                                    >


                                    <small
                                        style="
                                            color: var(--muted);
                                            font-family: monospace;
                                            font-size: 11px;
                                        "
                                    >

                                        ${
                                            escapeHtml(
                                                book.barcode || 'N/A'
                                            )
                                        }

                                    </small>

                                </div>


                                <div>

                                    <span class="detail-label">
                                        Location
                                    </span>

                                    ${
                                        escapeHtml(
                                            book.section_location ||
                                            'General'
                                        )
                                    }

                                </div>


                            </div>

                        </div>

                    </div>

                </a>

            `;

        }



        /* =====================================================
           ESCAPE HTML
           ===================================================== */

        function escapeHtml(str) {

            const div =
                document.createElement('div');

            div.textContent = str;

            return div.innerHTML;

        }



        /* =====================================================
           DEBOUNCED SEARCH
           ===================================================== */

        searchInput.addEventListener(
            'input',
            function() {

                clearTimeout(debounceTimer);


                debounceTimer =
                    setTimeout(
                        doSearch,
                        300
                    );

            }
        );


        /* =====================================================
           ENTER KEY SEARCH
           ===================================================== */

        searchInput.addEventListener(
            'keydown',
            function(e) {

                if (e.key === 'Enter') {

                    clearTimeout(
                        debounceTimer
                    );

                    doSearch();

                }

            }
        );


        /* =====================================================
           SEARCH BUTTON
           ===================================================== */

        searchBtn.addEventListener(
            'click',
            doSearch
        );


        /* =====================================================
           FILTER CHANGE
           ===================================================== */

        filterType.addEventListener(
            'change',
            doSearch
        );


        filterAvailability.addEventListener(
            'change',
            doSearch
        );

    </script>


</body>

</html><?php /**PATH C:\laragon\www\LIBRARY-MANAGEMENT-SYSTEM\resources\views/opac.blade.php ENDPATH**/ ?>