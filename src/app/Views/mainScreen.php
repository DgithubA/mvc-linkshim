<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>دوره‌های آموزشی</title>
    <link href="/assets/css/out.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
<?php
include __DIR__ . "/templates/header.php";
?>

<!-- محتوا اصلی -->
<main class="container mx-auto px-6 py-10 flex-grow">
    <h1 class="text-3xl font-extrabold mb-8 text-center text-indigo-700">لیست دوره‌های آموزشی</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- نمونه کارت دوره -->
        <article class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
            <img src="/assets/images/php.png" alt="PHP Programming Bootcamp" class="h-40 w-full object-cover" />
            <div class="p-6 flex flex-col flex-grow">
                <h2 class="text-xl font-semibold mb-2">PHP Programming Bootcamp</h2>
                <p class="text-gray-600 flex-grow">Comprehensive PHP course from basics to advanced topics.</p>
                <div class="mt-4">
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-3 py-1 rounded-full">سطح: Beginner</span>
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full mr-2 rtl:mr-0 rtl:ml-2">قیمت: ۱۹۹,۰۰۰ تومان</span>
                </div>
            </div>
        </article>

        <article class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
            <img src="https://example.com/thumbs/ml.jpg" alt="Machine Learning Basics" class="h-40 w-full object-cover" />
            <div class="p-6 flex flex-col flex-grow">
                <h2 class="text-xl font-semibold mb-2">Machine Learning Basics</h2>
                <p class="text-gray-600 flex-grow">Intro to ML for beginners using Python.</p>
                <div class="mt-4">
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-3 py-1 rounded-full">سطح: Beginner</span>
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full mr-2 rtl:mr-0 rtl:ml-2">قیمت: ۲۵۰,۰۰۰ تومان</span>
                </div>
            </div>
        </article>

        <article class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
            <img src="https://example.com/thumbs/security.jpg" alt="Network Security Advanced" class="h-40 w-full object-cover" />
            <div class="p-6 flex flex-col flex-grow">
                <h2 class="text-xl font-semibold mb-2">Network Security Advanced</h2>
                <p class="text-gray-600 flex-grow">Advanced techniques in network defense and ethical hacking.</p>
                <div class="mt-4">
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-3 py-1 rounded-full">سطح: Advanced</span>
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full mr-2 rtl:mr-0 rtl:ml-2">قیمت: ۴۰۰,۰۰۰ تومان</span>
                </div>
            </div>
        </article>

        <article class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
            <img src="https://example.com/thumbs/webdesign.jpg" alt="Web Design with HTML & CSS" class="h-40 w-full object-cover" />
            <div class="p-6 flex flex-col flex-grow">
                <h2 class="text-xl font-semibold mb-2">Web Design with HTML & CSS</h2>
                <p class="text-gray-600 flex-grow">Learn modern web design using HTML5 and CSS3.</p>
                <div class="mt-4">
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-3 py-1 rounded-full">سطح: Intermediate</span>
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full mr-2 rtl:mr-0 rtl:ml-2">قیمت: ۱۲۰,۰۰۰ تومان</span>
                </div>
            </div>
        </article>

    </div>
</main>
<?php
include __DIR__ . "/templates/footer.php";
?>

</body>
</html>
