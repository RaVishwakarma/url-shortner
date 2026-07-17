@if(session('success'))
    <p style="color:green">
        {{ session('success') }}
    </p>
@endif
<!DOCTYPE html>
<html>
<head>
    <title>Short URLs</title>
</head>
<body>

@if(auth()->user()->role !== 'super_admin')
<h2>Create Short URL</h2>

@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('short-urls.store') }}">
    @csrf

    <input
        type="text"
        name="original_url"
        placeholder="https://example.com"
        style="width:400px"
    >

    <button type="submit">
        Create
    </button>
</form>

<hr>
@endif

<h3>
    @if(auth()->user()->role === 'super_admin')
        All Short URLs
    @elseif(auth()->user()->role === 'admin')
        Company Short URLs
    @else
        Your Short URLs
    @endif
</h3>

@if($urls->isNotEmpty())
    <table border="1" cellpadding="8">

        <tr>
            <th>Original URL</th>
            <th>Short URL</th>
            @if(auth()->user()->role !== 'member')
                <th>Company</th>
                <th>Created By</th>
            @endif
        </tr>

        @foreach($urls as $url)

        <tr>
            <td>{{ $url->original_url }}</td>

            <td>
                <a href="{{ route('short-urls.redirect', $url->short_code) }}">
                    {{ route('short-urls.redirect', $url->short_code) }}
                </a>
            </td>
            @if(auth()->user()->role !== 'member')
                <td>{{ $url->company->name }}</td>
                <td>{{ $url->user->name }}</td>
            @endif

        </tr>

        @endforeach

    </table>
@else
    <p>No short URLs found.</p>
@endif
</body>
</html>
