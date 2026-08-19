<header class="bg-white border-b">

    <div class="max-w-6xl mx-auto h-16 flex justify-between items-center px-5">

        <a href="{{ route('jobseeker.home') }}"
           class="font-bold text-xl">
            DMDP
        </a>

        <nav class="flex gap-4">

            <a href="{{ route('jobseeker.home') }}">
                Home
            </a>

            <a href="{{ route('jobseeker.profile') }}">
                Profile
            </a>

        </nav>

        <div class="flex items-center gap-3">

            <span>
                {{ Auth::user()->full_name }}
            </span>

            <form action="{{ route('logout') }}" method="POST">

                @csrf

                <button type="submit"
                        class="text-red-600">
                    Logout
                </button>

            </form>

        </div>

    </div>

</header>