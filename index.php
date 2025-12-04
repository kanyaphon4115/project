<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PetHome</title>
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

<body class="bg-gradient-to-b from-[#f7d7a3] to-[#efbf82] min-h-screen">

  <nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">

        <!-- LOGO -->
        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31] tracking-wide">
            <div class="bg-white rounded-full shadow-md p-1 px-2">
                🐾
            </div>
            PetHome
        </h1>

        <!-- MENU (ขยับไปขวา) -->
        <ul class="flex space-x-6 text-sm font-semibold text-gray-900 ml-auto">
            <li><a href="#" class="hover:text-green-700">HOME</a></li>
            <li><a href="#" class="hover:text-green-700">FORM</a></li>
            <li><a href="#" class="hover:text-green-700">DONATE</a></li>
            <li><a href="#" class="hover:text-green-700">REQUEST STATUS</a></li>
            <!--  <li><a href="#" class="hover:text-green-700">PROFILE</a></li>-->
              <li>
        <a href="login.php"
           class="px-5 py-2 rounded-full bg-green-600 text-white font-semibold shadow-md hover:bg-green-700 transition">
           Login
        </a>
    </li>
        </ul>

    </div>
</nav>


    <!-- CONTENT -->
    <section class="flex flex-col items-center justify-center min-h-screen px-6 pt-24">

        <!-- PET IMAGE -->
        <img 
            src="dog.png" 
            alt="pet" 
            class="w-44 h-44 mt-6 animate-[float_3s_ease-in-out_infinite]"
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
