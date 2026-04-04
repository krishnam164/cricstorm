<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CricStrome | Elite Auction Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        midnight: '#05070A',  // Deepest Onyx
                        surface: '#0E1117',   // Elevated Surface
                        ember: '#FF4D00',     // Electric Orange Accent
                        silver: '#94A3B8'     // Muted Metallic Text
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&display=swap');
        body { font-family: 'Space Grotesk', sans-serif; background-color: #05070A; color: #FFFFFF; }
        
        /* Premium Glow Effect */
        .ember-glow:hover {
            box-shadow: 0 0 40px rgba(255, 77, 0, 0.15);
            border-color: rgba(255, 77, 0, 0.5);
        }
        .text-gradient {
            background: linear-gradient(to right, #FFFFFF, #FF4D00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>

    <nav class="sticky top-0 z-50 bg-midnight/80 backdrop-blur-xl border-b border-white/5 px-8 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="index.php" class="flex items-center space-x-4 group">
                <div class="p-2 bg-ember rounded-xl rotate-3 group-hover:rotate-0 transition-all duration-500 shadow-lg shadow-ember/20">
                    <img src="images/favicon.png" alt="Logo" class="h-8 w-auto brightness-0 invert">
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-bold tracking-tighter leading-none">CRIC<span class="text-ember">STROME</span></span>
                    <span class="text-[9px] uppercase tracking-[0.4em] font-bold text-silver">Auction Engine v2.0</span>
                </div>
            </a>

            <div class="hidden md:flex items-center space-x-10 text-[11px] font-bold uppercase tracking-widest">
                <a href="#" class="text-silver hover:text-white transition">Live Feed</a>
                <a href="#" class="text-silver hover:text-white transition">Schedule</a>
                <a href="admin/login.php" class="px-6 py-2.5 bg-white text-midnight rounded-full hover:bg-ember hover:text-white transition-all font-black">
                    Admin Portal
                </a>
            </div>
        </div>
    </nav>