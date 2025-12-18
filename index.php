<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PawHome</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
    /* Animation เด้งน้องหมา */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
</style>
</head>

<body class="min-h-screen bg-repeat relative" style="background-image: url('assets\images\bg_pethome_pattern.jpg'); background-size: 300px;">

<?php include __DIR__ . '/components/navbar.php';?>


    <!-- CONTENT -->
    <section class="relative z-10 flex flex-col items-center justify-center min-h-screen px-6 pt-24">

    <!-- PET IMAGE -->
<img 
    src="assets/images/icon_dog.png"
    alt="pet"
    class="w-56 h-56 mt-6 object-contain mx-auto 
           animate-[float_3s_ease-in-out_infinite]"
>


        <!-- TEXT -->
        <div class="text-center mt-8">
            <h2 class="text-3xl font-black text-gray-900 tracking-wide">
                Make A New Friends
            </h2>
            <p class="text-gray-700 mt-3 text-lg">
                A New Home For Your Four Legged Friend
            </p>
        </div>

        <!-- BUTTON -->
        <div class="mt-8">
            <a href="signup.php"
               class="px-8 py-3 rounded-full bg-green-600 text-white font-semibold shadow-lg hover:bg-green-700 transition">
               Adopt Now
            </a>
        </div>

    </section>

</body>
</html>
