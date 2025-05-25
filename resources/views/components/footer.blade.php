<footer id="footer" class="bg-white py-2 sm:py-3 md:py-4 text-center shadow-md w-full relative">
    <!-- Pembatas berupa garis yang merata sampai mentok pojok kiri dan kanan -->
    <div class="absolute top-0 left-0 right-0 flex justify-center -translate-y-1/2">
        <div class="flex items-center w-full">
            <div class="h-1 bg-blue-400 rounded-full flex-grow"></div>
            <div class="h-2 w-2 rounded-full bg-blue-400 mx-1 flex-shrink-0"></div>
            <div class="h-3 w-3 rounded-full bg-blue-500 mx-1 flex-shrink-0"></div>
            <div class="h-2 w-2 rounded-full bg-blue-400 mx-1 flex-shrink-0"></div>
            <div class="h-1 bg-blue-400 rounded-full flex-grow"></div>
        </div>
    </div>

    <div class="container mx-auto px-4 pt-2 pb-0">
        <div class="flex flex-col md:flex-row justify-between items-center md:items-start mb-3">
            <!-- Tentang Projek Ini - di tengah pada mobile, kiri pada desktop -->
            <div class="text-center md:text-left max-w-md mb-4 md:mb-0">
                <h3 class="font-bold text-sm md:text-base text-gray-800 mb-2">Tentang Projek Ini</h3>
                <p class="text-xs md:text-sm text-gray-600 mb-1">Lorem ipsum dolor sit amet consectetur adipiscing elit magna tincidunt.</p>
                <p class="text-xs md:text-sm text-gray-600">Donec ultrices velit nulla pharetra pulvinar diam commodo feugiat.</p>
            </div>
            
            <!-- Tim Pembuat - di tengah pada mobile, kanan pada desktop -->
            <div class="text-center md:text-right">
                <h3 class="font-bold text-sm md:text-base text-gray-800 mb-2 text-center md:text-right">Tim Pembuat</h3>
                <div class="flex justify-center md:justify-end gap-6">
                    <div class="flex flex-col items-center">
                        <a href="https://iqbalbagus-s.github.io/my-portfolio/" target="_blank" class="block">
                            <div class="w-8 h-8 bg-gray-300 rounded-full overflow-hidden mb-1">
                                <img src="/images/foto-iqbal.jpg" alt="Iqbal Bagus Satriawan" class="w-full h-full object-cover">
                            </div>
                        </a>
                        <a href="https://iqbalbagus-s.github.io/my-portfolio/" target="_blank" class="text-xs text-gray-600 hover:text-blue-500">Iqbal</a>
                    </div>
                    <div class="flex flex-col items-center">
                        <a href="https://reiirei.github.io/MyPortofolio/" target="_blank" class="block">
                            <div class="w-8 h-8 bg-gray-300 rounded-full overflow-hidden mb-1">
                                <img src="/images/foto-orie.jpg" alt="Orie Esra Yuwangga" class="w-full h-full object-cover">
                            </div>
                        </a>
                        <a href="https://reiirei.github.io/MyPortofolio/" target="_blank" class="text-xs text-gray-600 hover:text-blue-500">Orie</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Copyright -->
<div class="w-full bg-blue-600 py-2 text-center" style="border-radius: 12px 12px 0 0;">
    <p class="font-semibold text-xs sm:text-sm md:text-base text-white"> {{ date('Y') }} © PT Asia Pramulia</p>
</div>