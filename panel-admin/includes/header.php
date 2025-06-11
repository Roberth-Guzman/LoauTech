<div class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-900">
           <a href="../panel-principal.php" ><i class="fas fa-user-shield mr-2 text-blue-600"></i>
            Panel de Administración
        </h1></a>
        
        <div class="flex items-center space-x-4">
            <div class="relative">
                <i class="fas fa-bell text-gray-500 hover:text-gray-700 cursor-pointer"></i>
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
            </div>
            
            <div class="relative">
                <i class="fas fa-envelope text-gray-500 hover:text-gray-700 cursor-pointer"></i>
                <span class="absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">5</span>
            </div>
            
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user']['nombre'] ?? 'Admin') ?>&background=random" 
                     alt="Usuario" class="h-8 w-8 rounded-full">
                <span class="ml-2 text-sm font-medium text-gray-700"><?= $_SESSION['user']['nombre'] ?? 'Admin' ?></span>
            </div>
        </div>
    </div>
</div>