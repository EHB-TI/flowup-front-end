<template>
	<aside
		class="fixed flex flex-col items-center bg-red-500 text-gray-700 shadow h-full">
		<!-- Side Nav Bar-->

		<div class="h-20 flex items-center w-full">
			<a class="h-7 w-10 mx-auto" href="">
				<img
					class="h-7 w-10 mx-auto"
					src="/images/desiderius_logo_nbg_red.png" />
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
			<a href="https://frontend.hypere.be:4433/saml2/AD/logout">
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
					d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
				</svg>
			</a>
		</div>
	</aside>
</template>


