<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- ✅ สำคัญมากสำหรับ responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PawHome</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>

<body class="min-h-screen bg-repeat relative"
      style="background-image: url('assets/images/bg_pethome_pattern.jpg'); background-size: 300px;">

    <!-- OVERLAY -->
    <div class="absolute inset-0 bg-white/70 backdrop-blur-sm pointer-events-none"></div>

    <!-- NAVBAR -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- CONTENT -->
    <section class="relative z-10 min-h-screen px-4 sm:px-6 lg:px-10
                    pt-24 sm:pt-28 lg:pt-32
                    flex flex-col items-center justify-center text-center">

        <!-- PET IMAGE -->
        <img
            src="assets/images/icon_dog.png"
            alt="pet"
            class="w-40 h-40 sm:w-52 sm:h-52 lg:w-64 lg:h-64
                   object-contain mx-auto mt-2
                   animate-[float_3s_ease-in-out_infinite]"
        />

        <!-- TEXT -->
        <div class="mt-6 sm:mt-8 max-w-md sm:max-w-lg lg:max-w-xl">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 tracking-wide">
                Make A New Friends
            </h2>

            <p class="text-gray-700 mt-3 text-base sm:text-lg lg:text-xl leading-relaxed">
                A New Home For Your Four Legged Friend
            </p>
        </div>

        <!-- BUTTON -->
        <div class="mt-7 sm:mt-8">
            <a href="<?= isset($_SESSION['user_id']) ? 'homeped.php' : 'signup.php' ?>"
               class="inline-flex items-center justify-center
                      px-6 sm:px-8 py-3
                      rounded-full bg-green-600 text-white font-semibold
                      shadow-lg hover:bg-green-700 transition
                      text-base sm:text-lg">
                <?= isset($_SESSION['user_id']) ? 'Go to Home' : 'Adopt Now' ?>
            </a>
        </div>

    </section>
</body>
</html>
