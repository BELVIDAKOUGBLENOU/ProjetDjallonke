<x-mail::message>
    # Welcome to {{ $community->name }}

    Hello {{ $user->name }},

    You have been added to the community **{{ $community->name }}**.

    Here are your login credentials:

    **Email:** {{ $user->email }}<br>
    **Password:** {{ $password }}

    Please login and change your password immediately.

    <x-mail::button :url="route('login')">
        Login
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
