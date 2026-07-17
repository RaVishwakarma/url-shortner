<x-guest-layout>

<form method="POST">

@csrf

<h2>Create Account</h2>

<p>Email: {{ $invitation->email }}</p>

<input 
name="name"
placeholder="Name"
required
>


<input 
name="password"
type="password"
placeholder="Password"
required
>


<button>
Create Account
</button>

</form>

</x-guest-layout>