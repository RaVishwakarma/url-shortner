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

<h2>Create Short URL</h2>

@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="/short-urls">
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

<h3>Your URLs</h3>

@if(!empty($urls))
    <table border="1" cellpadding="8">

        <tr>
            <th>Original URL</th>
            <th>Short URL</th>
        </tr>

        @foreach($urls as $url)

        <tr>
            <td>{{ $url->original_url }}</td>

            <td>
                <a href="/{{ $url->short_code }}">
                    {{ url($url->short_code) }}
                </a>
            </td>

        </tr>

        @endforeach

    </table>
@endif
</body>
</html>