<x-layout>
    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-eve-accent selection:text-white">
        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex flex-col flex-wrap justify-center items-center">
                <h1 class="text-2xl font-bold text-eve-secondary text-center">Work In Progress</h1>
                <p class="text-white text-left leading-relaxed">
                    Fellow Capsuleer,
                    <br>
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;In the vast expanse of New Eden, every explorer seeks an edge. I'm currently
                    developing a side project, a dedicated companion app, to aid fellow Capsuleers like you in
                    navigating the complexities of Eve Online. As this venture is a personal endeavor, separate from my
                    main occupation, my time is somewhat limited.
                    <br>
                    <br>
                    The app is still unnamed, but its purpose is clear: to serve as an invaluable tool for Eve Online
                    enthusiasts. It will initially focus on enhancing skill queue management, followed by streamlining
                    planetary industry processes, elevating your interstellar journey.
                </p>

                <a href="{{ route('login') }}" role="button" class="mt-2">
                    <img src="{{ Vite::asset('resources/images/Eve SSO Login.png') }}"
                         alt="Login using the Eve Online SSO">
                </a>
            </div>
        </div>
    </div>
</x-layout>
