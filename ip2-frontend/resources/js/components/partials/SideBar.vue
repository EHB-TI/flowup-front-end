<template>
	<aside
		class="fixed flex flex-col items-center bg-red-500 text-gray-700 shadow h-full">
		<!-- Side Nav Bar-->

		<div class="h-16 flex items-center w-full">
			<a class="h-6 w-6 mx-auto" href="">
				<img
					class="h-6 w-6 mx-auto"
					src="https://o.remove.bg/downloads/f96ec957-fd72-4112-83e2-fff584dd82f1/letter-d-png-5a369b5a7e1573.7584737815135281545164-removebg-preview.png"
					alt="logo" />
			</a>
		</div>  

		<ul>
			<!-- Items Section -->
			<li class="hover:bg-red-400">
				<router-link
					to="/"
					class="h-16 px-6 flex justify-center items-center w-full">
					<svg 
                        xmlns="http://www.w3.org/2000/svg" 
                        class="h-8 w-8" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="white">
                        <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="1" 
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
				</router-link>
			</li>

			<li class="hover:bg-red-400">
				<router-link
					to="/create"
					class="h-16 px-6 flex justify-center items-center w-full">
					<svg 
						xmlns="http://www.w3.org/2000/svg" 
						class="h-8 w-8" 
						fill="none" 
						viewBox="0 0 24 24" 
						stroke="white">
						<path 
						stroke-linecap="round" 
						stroke-linejoin="round" 
						stroke-width="1" 
						d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
				</router-link>
			</li>

            <li class="hover:bg-red-400">
				<router-link
					to="/calendar"
					class="h-16 px-6 flex justify-center items-center w-full">
					<svg 
                        xmlns="http://www.w3.org/2000/svg" 
                        class="h-8 w-8" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="white">
                        <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="1" 
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
				</router-link>
			</li>

            <li class="hover:bg-red-400">
				<router-link
					to="/profile"
					class="h-16 px-6 flex justify-center items-center w-full">
					<svg 
                        xmlns="http://www.w3.org/2000/svg" 
                        class="h-8 w-8" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="white">
                        <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="1" 
                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
				</router-link>
			</li>
		</ul>

        <div class="mt-auto h-16 flex items-center w-full">
			<!-- Action Section -->
			<router-link
                to="/settings"
				class="h-16 mx-auto flex justify-center items-center w-full hover:bg-red-400">
				<svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    class="h-8 w-8" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="white">
                    <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="1" 
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="1" 
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>

			</router-link>
		</div>
	</aside>
</template>


